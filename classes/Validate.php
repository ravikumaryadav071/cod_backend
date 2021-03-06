<?php
class Validate {
	private $_passed = false,
			$_errors = array(),
			$_db = null;

	public function __construct() {
		$this->_db = DB::getInstance();
	}

	public function check($source, $items = array()) {
		foreach($items as $item => $rules) {
			foreach ($rules as $rule => $rule_value) {
				//echo "{$item} {$rule} must be {$rule_value}<br>";
				$value = $source[$item];
				//echo $value;

				if($rule === 'required' && empty($value)) {
					$this->addError("{$item} is required");
				} else if(!empty($value)) {
					switch ($rule) {
						case 'min':
								if(strlen($value) < $rule_value) {
									$this->addError("{$item} must be a minimum of {$rule_value} characters.");
								}
							break;

						case 'max':
								if(strlen($value) > $rule_value) {
									$this->addError("{$item} must be a max of {$rule_value} characters.");
								}
							break;

						case 'matches':
								if($value != $source[$rule_value]) {
									$this->addError("{$rule_value} must be equal to {$item}");

								}
							break;

						case 'unique':
								if(!strstr($value, ' ')){
									if(ctype_alpha(substr($value, 0, 1))){
										$check = $this->_db->get($rule_value, array($item, '=', $value));
										if($check->count()) {
											$this->addError("{$item} already exists.");
										}
									}else{
										$this->addError("First character of {$item} must be an alphabet.");	
									}
								}else{
									$this->addError("{$item} must not have spaces.");
								}
							break;

						case 'username':
								if(!strstr($value, ' ')){
									if(ctype_alpha(substr($value, 0, 1))){
										$check = $this->_db->get($rule_value, array($item, '=', $value));
										if($check->count()) {
											$this->addError("{$item} already exists.");
										}
									}else{
										$this->addError("First character of {$item} must be an alphabet.");	
									}
								}else{
									$this->addError("{$item} must not have spaces.");
								}
							break;
					}

				}
			}

		}

		if(empty($this->_errors)) {
			$this->_passed = true;
		}

		return $this;
	}

	private function addError($error) {
		$this->_errors[] = $error;
	}

	public function errors() {
		return $this->_errors;
	}

	public function passed() {
		return $this->_passed;
	}
}
?>