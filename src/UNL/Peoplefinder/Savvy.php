<?php

class UNL_Peoplefinder_Savvy extends Savvy
{
	public function removeGlobal($name)
	{
		if (isset($this->globals[$name])) {
			unset($this->globals[$name]);
		}

		return $this;
	}
}
