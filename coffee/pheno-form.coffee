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
    dupe = e.data && e.data.dupe
    $template = if dupe then $('.strain-tab.active') else $('.strain-tab.strain-tab-template')
    $tabTemplate = if dupe then $('#strain-tabs li.active') else $('#strain-tabs .strain-tab-template')
    newTabNum = 1 + parseInt($template.data('new-tab-num') || 0, 10)
    newId = 'strain-tab-' + newTabNum
    
    $('.strain-tab.active').removeClass('active').removeClass('in')
    $('#strain-tabs li.active').removeClass('active')
    
    $newContent = $template.clone().appendTo('#strain-tab-content')
    $newContent.removeClass('strain-tab-template').addClass('active in')
    $newContent.attr('id', newId)
    
    $newTab = $tabTemplate.clone().insertBefore($tabTemplate)
    $newTab.removeClass('strain-tab-template')
    $newTab.children('a').attr('href', '#' + newId).tab('show')
    
    $template.data('new-tab-num', newTabNum)
    
    $('#strain-tabs .add-strain-tab').addClass('hidden')
    newTabCount = $('.strain-tab:not(.strain-tab-template)').length
    $newContent.find('.tm-input').tagsManager({tagCloseIcon: '×'})
    $newContent.find('.editable').pasteImageReader (results) ->
      {event, dataURL} = results
      $('<p/>').append($('<img/>').attr('src', dataURL)).appendTo(this)
      setEndOfContentEditable(this)
  
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
  
  submitPhenotypes = (e) ->
    $('.strain-tab:not(.strain-tab-template) .editable').each ->
      $(this).next('input').val($(this).html())
      
  loadPhenotypes = (source) ->
    _.each(source, (v, k) ->
      $('form input[name='+k+']').val(v).attr('readonly', true)
    )
    if source.reviewed == '2'
      $('form input[name=done]').attr('checked', true)
    $('#strain-tab-content .delete-strain-tab').click() if !source.phenotypes.length
    _.each(source.phenotypes, (pheno, i) ->
      addStrainTab() if i > 0
      $tabContent = $('#strain-tab-content .strain-tab').last()
      
      _.each(pheno, (v, k) ->
        if v && m = k.match(/^mod_(\d)+$/)
          v = v.split(':', 2)
          $tabContent.find('[name="seg_'+m[1]+'[]"]').val(v[0]).change()
          v = v[1]
        if v && m = k.match(/^(ld50|eid50)$/)
          v = parseFloat(v).toExponential().split(/e/, 2)
          $tabContent.find('[name="'+m[0]+'_exp[]"]').val(parseFloat(v[1])).change()
          v = v[0]
        if k == 'clinical_qual' && v
          _.each(v.split(/,/g), (tag) ->
            $tabContent.find('.tm-input').tagsManager('pushTag',tag)
          )
        else if k == 'evidence' && v
          console.log(v)
          $tabContent.find('.editable').html(v)
        else
          $tabContent.find('[name="'+k+'[]"]').val(v).change()
      )
    )
  
  $('html').on 'click', '.add-strain-tab', addStrainTab
  $('form').on 'click', '.delete-strain-tab', deleteStrainTab
  $('html').on 'click', '.dupe-strain-tab', {dupe: true}, addStrainTab
  
  $('form').on 'keyup change', '[name="strain_name[]"]', (e) ->
    tab_id = $(this).closest('.strain-tab').attr('id')
    strain_id = $(this).val()
    $('#strain-tabs a[href=#'+tab_id+'] .strain-id').text(strain_id)

  $('form').on 'keyup change', '[name="animal[]"]', (e)->
    tab_id = $(this).closest('.strain-tab').attr('id')
    animal = $(this).val()
    $('#strain-tabs a[href=#'+tab_id+'] .animal').text(animal)
  
  $('form').on 'change select', '.seg-select', (e) ->
    val = $(this).val()
    $following_rows = $(this).closest('.controls').nextAll('.controls')
    if val != ''
      $following_rows.removeClass('hidden').find('.seg-select').eq(0).change()
    else
      $following_rows.addClass('hidden').find('.seg-select').eq(0).change()
  
  $('form').on('submit', submitPhenotypes)
  
  if !$('.strain-tab:not(.strain-tab-template)').length
    $('.add-strain-tab').eq(0).click()
  
  if SOURCE then loadPhenotypes(SOURCE)
  
)