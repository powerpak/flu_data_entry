<?php

class Phenotype extends Model {
  
  public function source() {
    return $this->belongs_to('Source');
  }
  
}