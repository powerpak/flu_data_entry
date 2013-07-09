$(->
  
  setEndOfContentEditable = (contentEditableElement) ->
    if document.createRange             # Firefox, Chrome, Opera, Safari, IE 9+
      range = document.createRange()
      range.selectNodeContents(contentEditableElement)
      range.collapse(false)
      selection = window.getSelection()
      selection.removeAllRanges()
      selection.addRange(range);
    else if document.selection          # IE 8 and lower
      range = document.body.createTextRange()
      range.moveToElementText(contentEditableElement)
      range.collapse(false)
      range.select()
  
  addStrainTab = (e) ->
    e.preventDefault() if e
    dupe = e? && e.data? && e.data.dupe
    $template = $('.strain-tab.strain-tab-template')
    $contentTemplate = if dupe then $('.strain-tab.active') else $template
    $tabTemplate = if dupe then $('#strain-tabs li.active') else $('#strain-tabs .strain-tab-template')
    newTabNum = 1 + parseInt($template.data('new-tab-num') || 0, 10)
    newId = 'strain-tab-' + newTabNum
    
    $('.strain-tab.active').removeClass('active').removeClass('in')
    $('#strain-tabs li.active').removeClass('active')
    
    $newContent = $contentTemplate.clone().appendTo('#strain-tab-content')
    $newContent.removeClass('strain-tab-template').addClass('active in')
    $newContent.attr('id', newId)
    
    $newTab = $tabTemplate.clone().insertBefore('#strain-tabs .strain-tab-template')
    $newTab.removeClass('strain-tab-template')
    $newTab.children('a').attr('href', '#' + newId).tab('show')
    
    $template.data('new-tab-num', newTabNum)
    
    $('#strain-tabs').sortable('refresh');
    $('#strain-tabs .add-strain-tab').addClass('hidden')
    newTabCount = $('.strain-tab:not(.strain-tab-template)').length
    $newContent.find('.tm-input').tagsManager({tagCloseIcon: '×'})
    $newContent.find('.editable').pasteImageReader (results) ->
      {event, dataURL} = results
      $('<p/>').append($('<img/>').attr('src', dataURL)).appendTo(this)
      setEndOfContentEditable(this)
    
    # For some reason <select> values are not copied with use of .clone()
    if dupe
      $newContent.find('select').each ->
        $(this).val($contentTemplate.find('[name="'+$(this).attr('name')+'"]').val()).change()
      $newContent.find('.tm-tag').remove()
      _.each $contentTemplate.find('[name="hidden-clinical_qual[]"]').val().split(/,/g), (tag) ->
        $newContent.find('.tm-input').tagsManager('pushTag', tag)
  
  deleteStrainTab = (e) ->
    e.preventDefault() if e
    $tabContent = $(this).closest('.strain-tab')
    $tab = $("#strain-tabs a[href='##{$tabContent.attr('id')}']").closest('li')
    $allTabs = $('#strain-tabs li:not(.strain-tab-template)')
    tabIndex = $allTabs.index($tab)
    $tab.remove()
    $tabContent.remove()
    if $allTabs.length > 1
      $('#strain-tabs li:not(.strain-tab-template) a').eq(tabIndex - 1).tab('show')
    $('#strain-tabs .add-strain-tab').toggleClass('hidden', $allTabs.length != 1)
  
  reorderStrainTabs = (e, ui) ->
    $allTabs = $('#strain-tabs li:not(.strain-tab-template) a')
    $lastTabContent = null
    $allTabs.each (i) ->
      $tabContent = $($(this).attr('href'))
      if i > 0
        $tabContent.insertAfter($lastTabContent)
      else
        $('#strain-tab-content').prepend($tabContent)
      $lastTabContent = $tabContent
  
  submitPhenotypes = (e) ->
    $('.strain-tab:not(.strain-tab-template) .editable').each ->
      $(this).next('input').val($(this).html())
      
  loadPhenotypes = (source) ->
    _.each source, (v, k) ->
      $('form input[name='+k+']').val(v).attr('readonly', true)
    if source.reviewed == '2'
      $('form input[name=done]').attr('checked', true)
    $('#strain-tab-content .delete-strain-tab').click() if !source.phenotypes.length
    _.each source.phenotypes, (pheno, i) ->
      addStrainTab() if i > 0
      $tabContent = $('#strain-tab-content .strain-tab').last()
      
      _.each pheno, (v, k) ->
        if v && m = k.match(/^mod_(\d)+$/)
          v = v.split(':', 2)
          $tabContent.find('[name="seg_'+m[1]+'[]"]').val(v[0]).change()
          v = v[1]
        if v && m = k.match(/^(ld50|eid50)$/)
          v = parseFloat(v).toExponential().split(/e/, 2)
          $tabContent.find('[name="'+m[0]+'_exp[]"]').val(parseFloat(v[1])).change()
          v = v[0]
        if k == 'clinical_qual' && v
          _.each v.split(/,/g), (tag) ->
            $tabContent.find('.tm-input').tagsManager('pushTag', tag)
        else if k == 'evidence' && v
          $tabContent.find('.editable').html(v)
        else
          $tabContent.find('[name="'+k+'[]"]').val(v).change()
      
  
  $('html').on 'click', '.add-strain-tab', addStrainTab
  $('form').on 'click', '.delete-strain-tab', deleteStrainTab
  $('html').on 'click', '.dupe-strain-tab', {dupe: true}, addStrainTab
  $('#strain-tabs').sortable({
    stop: reorderStrainTabs
  }).disableSelection();
  
  $('form').on 'keyup change', '[name="strain_name[]"]', (e) ->
    tabId = $(this).closest('.strain-tab').attr('id')
    strainId = $(this).val()
    $('#strain-tabs a[href=#'+tabId+'] .strain-id').text(strainId)

  $('form').on 'keyup change', '[name="animal[]"]', (e)->
    tabId = $(this).closest('.strain-tab').attr('id')
    animal = $(this).val()
    $('#strain-tabs a[href=#'+tabId+'] .animal').text(animal)
  
  $('form').on 'change select', '.seg-select', (e) ->
    val = $(this).val()
    $allSelects = $(this).closest('.control-group').find('.seg-select')
    $toHide = $allSelects.filter(-> $(this).val() != '').last().closest('.controls').nextAll('.controls').slice(1)
    allVals = _.uniq _.compact _.map $allSelects, (el) -> $(el).val()
    tabId = $(this).closest('.strain-tab').attr('id')
    $('#strain-tabs a[href=#'+tabId+'] .mods').text(if allVals.length then '~' + allVals.join(',') else '')
    if $toHide.length || allVals.length
      $allSelects.closest('.controls').not($toHide).removeClass('hidden')
      $toHide.addClass('hidden')
    else # show only the first row
      $allSelects.closest('.controls').addClass('hidden').eq(0).removeClass('hidden')
  
  $('form').on('submit', submitPhenotypes)
  
  if !$('.strain-tab:not(.strain-tab-template)').length
    $('.add-strain-tab').eq(0).click()

  $(document).on "keydown keypress", (e) ->
    rx = /INPUT|SELECT|TEXTAREA/i
    $targ = $(e.target)
    # e.which of 8 == backspace, and we don't want to do this when within contenteditables
    if e.which == 8 && !$targ.closest('[contenteditable=true]').length
      if (
        !rx.test(e.target.tagName) or e.target.disabled or e.target.readOnly or
        $targ.is(':checkbox,:radio:,:submit')
      )
        e.preventDefault()
  
  if SOURCE then loadPhenotypes(SOURCE)
  
)