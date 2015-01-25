<?php

class MyTemplate {
	private $template = [];

	public function __construct(array $template = array()) {
		$this->template = $template;
	}

	public function prepare($key, $default) {
		$this->template[$key] = $default;
		return $this;
	}

	public function set($key, $value) {
		/*$key = (array)$key;
		$pointer = $this->template;
		foreach ($key as $step) {
			if (isset($pointer[$step]))
				throw new InvalidArgumentException("Try to set undeclared template attribute ".$step.". Road: ".implode(' -> ', $key));

			$pointer = $pointer[$step];
		}*/

		if (!isset($this->template[$key])){
			throw new InvalidArgumentException("Try to set undeclared template attribute ".$key);
		}

		$this->template[$key] = $value;
		return $this;
	}

	public function __set($key, $value) {
		$this->set($key, $value);
	}

	public function render() {
		foreach ($this->template as &$value) {
			if( is_object($value) and get_class($value) == 'MyTemplate' )
				$value = $value->render();
		}
		return $this->template;
	}

}