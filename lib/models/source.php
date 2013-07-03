<?php

class Source extends Model {
  
  public function phenotypes() {
    return $this->has_many('Phenotype');
  }
  
  public static function save_from_form($post) {
    Model::start_transaction();
    $id = isset($post['id']) && $post['id'] !== '' ? $post['id'] : NULL;
    if ($id === NULL) {
      $sources = Model::factory('Source')->where('first_author', $post['first_author']);
      if ($post['pub_year']) { $sources->where('pub_year', $post['pub_year']); }
      else { $sources->where('pub_year', NULL); }
      $sources = $sources->find_many();
      if (count($sources) > 1) { throw new ApplicationException('Duplicate entry for author/year'); }
      if (!count($sources)) { throw new ApplicationException('That author/year was not found'); }
      $source = $sources[0];
    } else {
      $source = Model::factory('Source')->find_one($id);
      if (!$source->loaded()) { throw new ApplicationException('That source was not found'); }
    }
    foreach($source->phenotypes()->find_many() as $phenotype) {
      $phenotype->delete();
    }
    if (is_array($post['strain_name'])) foreach($post['strain_name'] as $i=>$strain_name) {
      $phenotype = new Phenotype;
      $phenotype->source_id = $source->id;
      $phenotype->strain_name = $strain_name;
      $FIELDS = array('animal', 'animal_subtype',
        'ld50', 'ld50_operator', 'eid50', 'eid50_operator',
        'trans_contact', 'trans_contact_qual',
        'trans_aerosol', 'trans_aerosol_qual',
        'pathogenicity_qual', 'evidence');
      $FLOATS_WITH_EXP = array('ld50', 'eid50');
      $FLOATS = array('trans_contact', 'trans_aerosol');
      foreach($FIELDS as $field) {
        if (is_array($post[$field])) {
          $val = $post[$field][$i];
          if (in_array($field, $FLOATS_WITH_EXP)) {
            $exp = $post["{$field}_exp"][$i];
            if ($val !== '' || $exp !== '') {
              $val = $val === '' ? '1.0' : $val;
              $val = floatval($val) * pow(10, floatval($exp));
            } else { continue; }
          }
          if (in_array($field, $FLOATS)) {
            if ($val === '') { continue; }
          }
          $phenotype->$field = $val;
        }
      }
      $phenotype->clinical_qual = $post['hidden-clinical_qual'][$i];
      for ($j = 1; $j <= 8; $j++) {
        if (is_array($post["seg_$j"]) && $post["seg_$j"][$i]) {
          $phenotype->{"mod_$j"} = $post["seg_$j"][$i] . ':' . $post["mod_$j"][$i];
        } else {
          $phenotype->{"mod_$j"} = NULL;
        }
      }
      $phenotype->save();
    }
    $source->reviewed = $post['done'] ? 2 : 1;
    $source->save();
    Model::commit();
    return $source;
  }
  
}