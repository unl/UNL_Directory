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

	public function renderXmlNode($nodeName, $nodeValue)
	{
		if (!$nodeValue) {
	        return;
	    }

	    if ($nodeValue instanceof Traversable) {
	        $hasNamedChildren = false;

	        foreach ($nodeValue as $childNodeName => $childNodeValue) {
	            if (!$hasNamedChildren && is_numeric($childNodeName)) {
	                $this->renderXmlNode($nodeName, $childNodeValue);
	                continue;
	            }

	            if (!$hasNamedChildren) {
	                echo '<' . $nodeName . '>';
	            }

	            $hasNamedChildren = true;
	            $this->renderXmlNode($childNodeName, $childNodeValue);
	        }

	        if ($hasNamedChildren) {
	            echo '</' . $nodeName . '>';
	        }

	        return;
	    }

	    echo '<' . $nodeName . '>' . $nodeValue . '</' . $nodeName . '>';
	}
}
