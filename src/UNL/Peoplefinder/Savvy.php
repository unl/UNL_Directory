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

    protected function fetch($mixed, $template = null)
    {
        if (!$template) {
            $object = $mixed;
            if ($mixed instanceof Savvy_ObjectProxy) {
                $object = $mixed->getRawObject();
            }

            if ($object instanceof Exception) {
                $template = $this->getClassToTemplateMapper()->map('Exception');
            }
        }

        return parent::fetch($mixed, $template);
    }
}
