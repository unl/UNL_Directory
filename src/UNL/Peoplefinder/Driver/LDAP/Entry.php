<?php

class UNL_Peoplefinder_Driver_LDAP_Entry extends ArrayObject
{
	public function __construct(array $entry)
	{
		$entry = self::normalizeEntry($entry);
		parent::__construct($entry, ArrayObject::ARRAY_AS_PROPS);
	}

	protected static function normalizeEntry(array $entry)
	{
		$entry = UNL_Peoplefinder_Driver_LDAP_Util::filterArrayByKeys($entry, 'is_string');
        unset($entry['count']);
        foreach ($entry as $attribute => $value) {
            if (is_array($value)) {
                $value = new UNL_Peoplefinder_Driver_LDAP_Multivalue($value);
                $entry[$attribute] = $value;
            }
        }

        return $entry;
	}

	public function append($value)
	{
		throw new Exception('Unimplemented');
	}

	public function exchangeArray($input)
	{
		$input = self::normalizeEntry($input);
		return parent::exchangeArray($input);
	}

	public function offsetExists($index)
	{
		$index = strtolower($index);
		return parent::offsetExists($index);
	}

	public function offsetGet($index)
	{
		$index = strtolower($index);
		return parent::offsetGet($index);
	}

	public function offsetSet($index, $newval)
	{
		$index = strtolower($index);
		return parent::offsetSet($index, $newval);
	}

	public function offsetUnset($index)
	{
		$index = strtolower($index);
		return parent::offsetUnset($index);
	}
}
