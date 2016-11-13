<?php

class Validate{
	private $_passed = false,
			$_error = [],
			$_db = null;


	public function __construct(){
		$this->_db = DB::getInstance();
	}

/*	public function check(Input $source,$items = array()){
		foreach($items as $item => $rules){
			$value = $source[$item];

			foreach($rules as $rule => $ruleValue){
				if($rule === 'required' && empty($value)){
					$this->newError("{$item} is required");
				} else {

				}

			}

		}

		if(empty($this->_error)){
			$this->_passed = true;
		}
		return $this;
	}*/

	public function check(Input $source,$args){

		$inputArr = $source->get();

		foreach($args as $item=>$rules){
			if(!isset($inputArr[$item])){throw new LogicException("Unknow input element's name provided,{$item};");};
			$inputVal = $inputArr[$item];
			foreach($rules as $ruleArr){
				$rule = $ruleArr[0];
				$ruleVal=$ruleArr[1][0];
				$errorMsg=$ruleArr[1][1];
				$noErr = true;
				switch($rule){
					case 'required'	: $noErr=!empty($inputVal) == $ruleVal;
					break;
					case 'match'	: $noErr=$inputVal === $inputArr[$ruleVal];
					break;
					case 'regexp'	: $noErr=preg_match($ruleVal,$inputVal); 
										if($noErr===false) throw new LogicException('Regexp check required at {$item} cause an error.');
					break;
					case 'unique'	: $dbCheck = $this->_db->get($ruleVal,array($item,'=',$inputVal));
										$noErr=$dbCheck->count()<1;		

					default: '';
				}
				if($noErr==false)$this->_error[$item]=$errorMsg;
			}
		}
		if(empty($this->_error)){
			$this->_passed = true;
		}
		return $this;
	}


	private function newError($error){
		$this->_error[] = $error;
	}

	public function error(){
		return $this->_error;
	}

	public function passed(){
		return $this->_passed;
	}
}


