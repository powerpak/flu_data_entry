// Generated by CoffeeScript 1.6.3
(function() {
  $(function() {
    var addStrainTab, deleteStrainTab, loadPhenotypes, reorderStrainTabs, setEndOfContentEditable, submitPhenotypes;
    setEndOfContentEditable = function(contentEditableElement) {
      var range, selection;
      if (document.createRange) {
        range = document.createRange();
        range.selectNodeContents(contentEditableElement);
        range.collapse(false);
        selection = window.getSelection();
        selection.removeAllRanges();
        return selection.addRange(range);
      } else if (document.selection) {
        range = document.body.createTextRange();
        range.moveToElementText(contentEditableElement);
        range.collapse(false);
        return range.select();
      }
    };
    addStrainTab = function(e) {
      var $contentTemplate, $newContent, $newTab, $tabTemplate, $template, dupe, newId, newTabCount, newTabNum;
      if (e) {
        e.preventDefault();
      }
      dupe = (e != null) && (e.data != null) && e.data.dupe;
      $template = $('.strain-tab.strain-tab-template');
      $contentTemplate = dupe ? $('.strain-tab.active') : $template;
      $tabTemplate = dupe ? $('#strain-tabs li.active') : $('#strain-tabs .strain-tab-template');
      newTabNum = 1 + parseInt($template.data('new-tab-num') || 0, 10);
      newId = 'strain-tab-' + newTabNum;
      $('.strain-tab.active').removeClass('active').removeClass('in');
      $('#strain-tabs li.active').removeClass('active');
      $newContent = $contentTemplate.clone().appendTo('#strain-tab-content');
      $newContent.removeClass('strain-tab-template').addClass('active in');
      $newContent.attr('id', newId);
      $newTab = $tabTemplate.clone().insertBefore('#strain-tabs .strain-tab-template');
      $newTab.removeClass('strain-tab-template');
      $newTab.children('a').attr('href', '#' + newId).tab('show');
      $template.data('new-tab-num', newTabNum);
      $('#strain-tabs').sortable('refresh');
      $('#strain-tabs .add-strain-tab').addClass('hidden');
      newTabCount = $('.strain-tab:not(.strain-tab-template)').length;
      $newContent.find('.tm-input').tagsManager({
        tagCloseIcon: '×'
      });
      $newContent.find('.editable').pasteImageReader(function(results) {
        var dataURL, event;
        event = results.event, dataURL = results.dataURL;
        $('<p/>').append($('<img/>').attr('src', dataURL)).appendTo(this);
        return setEndOfContentEditable(this);
      });
      if (dupe) {
        $newContent.find('select').each(function() {
          return $(this).val($contentTemplate.find('[name="' + $(this).attr('name') + '"]').val()).change();
        });
        return _.each($contentTemplate.find('[name="clinical_qual[]"]').val().split(/,/g), function(tag) {
          console.log(tag);
          return $newContent.find('.tm-input').tagsManager('pushTag', tag);
        });
      }
    };
    deleteStrainTab = function(e) {
      var $allTabs, $tab, $tabContent, tabIndex;
      if (e) {
        e.preventDefault();
      }
      $tabContent = $(this).closest('.strain-tab');
      $tab = $("#strain-tabs a[href='#" + ($tabContent.attr('id')) + "']").closest('li');
      $allTabs = $('#strain-tabs li:not(.strain-tab-template)');
      tabIndex = $allTabs.index($tab);
      $tab.remove();
      $tabContent.remove();
      if ($allTabs.length > 1) {
        $('#strain-tabs li:not(.strain-tab-template) a').eq(tabIndex - 1).tab('show');
      }
      return $('#strain-tabs .add-strain-tab').toggleClass('hidden', $allTabs.length !== 1);
    };
    reorderStrainTabs = function(e, ui) {
      var $allTabs, $lastTabContent;
      $allTabs = $('#strain-tabs li:not(.strain-tab-template) a');
      $lastTabContent = null;
      return $allTabs.each(function(i) {
        var $tabContent;
        $tabContent = $($(this).attr('href'));
        if (i > 0) {
          $tabContent.insertAfter($lastTabContent);
        } else {
          $('#strain-tab-content').prepend($tabContent);
        }
        return $lastTabContent = $tabContent;
      });
    };
    submitPhenotypes = function(e) {
      return $('.strain-tab:not(.strain-tab-template) .editable').each(function() {
        return $(this).next('input').val($(this).html());
      });
    };
    loadPhenotypes = function(source) {
      _.each(source, function(v, k) {
        return $('form input[name=' + k + ']').val(v).attr('readonly', true);
      });
      if (source.reviewed === '2') {
        $('form input[name=done]').attr('checked', true);
      }
      if (!source.phenotypes.length) {
        $('#strain-tab-content .delete-strain-tab').click();
      }
      return _.each(source.phenotypes, function(pheno, i) {
        var $tabContent;
        if (i > 0) {
          addStrainTab();
        }
        $tabContent = $('#strain-tab-content .strain-tab').last();
        return _.each(pheno, function(v, k) {
          var m;
          if (v && (m = k.match(/^mod_(\d)+$/))) {
            v = v.split(':', 2);
            $tabContent.find('[name="seg_' + m[1] + '[]"]').val(v[0]).change();
            v = v[1];
          }
          if (v && (m = k.match(/^(ld50|eid50)$/))) {
            v = parseFloat(v).toExponential().split(/e/, 2);
            $tabContent.find('[name="' + m[0] + '_exp[]"]').val(parseFloat(v[1])).change();
            v = v[0];
          }
          if (k === 'clinical_qual' && v) {
            return _.each(v.split(/,/g), function(tag) {
              return $tabContent.find('.tm-input').tagsManager('pushTag', tag);
            });
          } else if (k === 'evidence' && v) {
            return $tabContent.find('.editable').html(v);
          } else {
            return $tabContent.find('[name="' + k + '[]"]').val(v).change();
          }
        });
      });
    };
    $('html').on('click', '.add-strain-tab', addStrainTab);
    $('form').on('click', '.delete-strain-tab', deleteStrainTab);
    $('html').on('click', '.dupe-strain-tab', {
      dupe: true
    }, addStrainTab);
    $('#strain-tabs').sortable({
      stop: reorderStrainTabs
    }).disableSelection();
    $('form').on('keyup change', '[name="strain_name[]"]', function(e) {
      var strainId, tabId;
      tabId = $(this).closest('.strain-tab').attr('id');
      strainId = $(this).val();
      return $('#strain-tabs a[href=#' + tabId + '] .strain-id').text(strainId);
    });
    $('form').on('keyup change', '[name="animal[]"]', function(e) {
      var animal, tabId;
      tabId = $(this).closest('.strain-tab').attr('id');
      animal = $(this).val();
      return $('#strain-tabs a[href=#' + tabId + '] .animal').text(animal);
    });
    $('form').on('change select', '.seg-select', function(e) {
      var $followingRows, allVals, tabId, val;
      val = $(this).val();
      $followingRows = $(this).closest('.controls').nextAll('.controls');
      allVals = _.uniq(_.compact(_.map($(this).closest('.control-group').find('.seg-select'), function(el) {
        return $(el).val();
      })));
      tabId = $(this).closest('.strain-tab').attr('id');
      $('#strain-tabs a[href=#' + tabId + '] .mods').text(allVals.length ? '~' + allVals.join(',') : '');
      if (val !== '') {
        return $followingRows.removeClass('hidden').find('.seg-select').eq(0).change();
      } else {
        return $followingRows.addClass('hidden').find('.seg-select').eq(0).change();
      }
    });
    $('form').on('submit', submitPhenotypes);
    if (!$('.strain-tab:not(.strain-tab-template)').length) {
      $('.add-strain-tab').eq(0).click();
    }
    if (SOURCE) {
      return loadPhenotypes(SOURCE);
    }
  });

}).call(this);
