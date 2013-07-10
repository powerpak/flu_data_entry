<?php include 'partial/header.php' ?>
<?php include 'partial/typeahead_data.php' ?>

  <div class="container">

    <form action="<?= href('/edit') ?>" method="post">
      <div class="form-inline">
        <label>
          <span class="little">Author</span>
          <input type="text" name="first_author" size="50"/>
        </label>
        <label>
          <span class="little">Year</span>
          <input type="text" name="pub_year" class="span1" placeholder="2013"/>
        </label>
        <label class="checkbox im-done">
          <input type="checkbox" name="done"/>
          <strong>This record is complete</strong>
        </label>
        <button type="submit" class="btn btn-primary btn-large pull-right" name="save">Save</button>
      </div>
      
      <ul class="nav nav-tabs" id="strain-tabs">
        <li class="strain-tab-template">
          <a href="#" data-toggle="tab">
            <span class="strain-id">Unknown Strain</span><strong class="mods"></strong> <strong class="cross">&rarr;</strong> <strong class="animal">mouse</strong>
          </a>
        </li>
        <button class="btn add-strain-tab">Add another experiment</button>
      </ul>
      
      <input type="hidden" name="id"/>
      
      <div id="strain-tab-content" class="tab-content">
        
      </div>
      
    </form>
    
    <div class="strain-tab tab-pane form-horizontal strain-tab-template" data-new-tab-num="0">
      <div class="control-group">
        <label class="control-label" for="strain_name[]">Strain ID</label>
        <div class="controls form-inline">
          <input type="text" placeholder="A/goose/Guangdong/96" name="strain_name[]"/>
          <label>
            <span class="little"><strong class="cross">&rarr;&nbsp;</strong> animal</span>
            <select name="animal[]" class="span2">
              <option>mouse</option>
              <option>ferret</option>
              <option>chicken</option>
              <option>pig</option>
            </select>
          </label>
          <label>
            subtype
            <input type="text" name="animal_subtype[]" class="span1" data-provide="typeahead" 
              placeholder="balb/c" data-source="<?= html_esc(json_encode($ANIMAL_SUBTYPES)) ?>" autocomplete="off"/>
          </label>
        </div>
        <div class="controls">
          <button class="btn btn-danger delete-strain-tab">Delete this experiment</button>
          <button class="btn add-strain-tab">Add another experiment</button>
          <button class="btn dupe-strain-tab">Duplicate this experiment</button>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="mod_1[]">Modifications</label>
        <?php for($i = 1; $i <= 8; $i++): ?>
        <div class="controls<?= $i == 1 ? '' : ' hidden' ?>">
          <strong><?= $i ?></strong>
          <select name="seg_<?= $i ?>[]" class="seg-select span2">
            <option></option>
            <option>PB1</option>
            <option>PB1-F2</option>
            <option>PB2</option>
            <option>PA</option>
            <option>HA</option>
            <option>NP</option>
            <option>NA</option>
            <option>M1</option>
            <option>M2</option>
            <option>NS1</option>
            <option>NS2</option>
          </select>
          <input type="text" class="span4" name="mod_<?= $i ?>[]"
            placeholder="<?= $i == 1 ? 'e.g. K144V or A/Norway/3568/2009' : '' ?>"/>
        </div>
        <?php endfor; ?>
      </div>
      <div class="control-group">
        <label class="control-label" for="ld50[]">Dosages</label>
        <div class="controls form-inline">
          <label for="ld50[]">
            <span class="little">LD50</span>
          </label>
          <select name="ld50_operator[]" class="span1">
            <option></option>
            <option>&gt;</option>
            <option>&ge;</option>
            <option>&lt;</option>
            <option>&le;</option>
          </select>
          <input type="text" name="ld50[]" class="span1" placeholder="1.0"/>
          ×10^
          <input type="text" name="ld50_exp[]" class="span1" placeholder="5"/>
          <select name="ld50_units[]" class="span2">
            <option selected>PFU</option>
            <option>EID50</option>
            <option>TCID50</option>
            <option>CCID50</option>
          </select>
        </div>
        <div class="controls form-inline">  
          <label for="eid50[]">
            <span class="little">EID50</span>
          </label>
          <select name="eid50_operator[]" class="span1">
            <option></option>
            <option>&gt;</option>
            <option>&ge;</option>
            <option>&lt;</option>
            <option>&le;</option>
          </select>
          <input type="text" name="eid50[]" class="span1" placeholder="1.0"/>
          ×10^
          <input type="text" name="eid50_exp[]" class="span1" placeholder="5"/>
          PFU
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="trans_contact[]">Transmission %</label>
        <div class="controls form-inline">
          <label>
            <span class="little">contact</span>
            <input type="text" name="trans_contact[]" class="span1"/>
            <select name="trans_contact_qual[]" class="span1">
              <option></option>
              <option>no</option>
              <option>yes</option>
              <option>low</option>
              <option>med</option>
              <option>high</option>
            </select>
          </label>
          <label>
            <span class="little">aerosol</span>
            <input type="text" name="trans_aerosol[]" class="span1"/>
            <select name="trans_aerosol_qual[]" class="span1">
              <option></option>
              <option>no</option>
              <option>yes</option>
              <option>low</option>
              <option>med</option>
              <option>high</option>
            </select>
          </label>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="pathogenicity_qual[]">Overall pathogenicity</label>
        <div class="controls form-inline">
          <select name="pathogenicity_qual[]" class="span2">
            <option></option>
            <option>low</option>
            <option>intermediate</option>
            <option>high</option>
          </select>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="clinical_qual[]">Clinical signs</label>
        <div class="controls form-inline">
          <input type="text" name="clinical_qual[]" placeholder="e.g., playful, happy" class="tm-input" autocomplete="off" data-provide="typeahead" data-source="<?= html_esc(json_encode($CLINICAL_TAGS)) ?>"/>
        </div>
      </div>
      <div class="control-group">
        <label class="control-label" for="evidence[]">Evidence</label>
        <div class="controls form-inline">
          <div class="editable span8 well" contenteditable="true">
          </div>
          <input type="hidden" name="evidence[]"/>
        </div>
      </div>
    </div>

  </div>
  
  <?php if (isset($source)): ?>
  <script>
    var SOURCE = <?= json_encode($source->as_array()); ?>;
    SOURCE.phenotypes = <?= json_encode($phenotypes); ?>;
  </script>
  <?php endif; ?>
  
  <?php include 'partial/scripts.php' ?>
  <script type="text/javascript" src="<?= href('js/pheno-form.js') ?>"></script>
</body>
</html>