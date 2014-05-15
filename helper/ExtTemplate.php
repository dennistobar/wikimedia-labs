<?php

namespace helper;

class ExtTemplate extends \Template {

	public function url($val) {
		return rawurlencode($val);
	}
}
