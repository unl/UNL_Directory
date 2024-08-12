<?php

class UNL_Peoplefinder_Driver_LDAP_Multivalue extends ArrayIterator implements JsonSerializable
{
	public function __construct(array $attribute)
	{
		// only use the numeric keys
		$attribute = UNL_Peoplefinder_Driver_LDAP_Util::filterArrayByKeys($attribute, "is_int");
		parent::__construct($attribute);
	}

	public function jsonSerialize(): mixed
	{
		return $this->getArrayCopy();
	}

	/**
     * Returns the first attribute entry
     *
     * @return string
     */
    public function __toString(): string
    {
        $firstValue = $this[0];
        return (string) $firstValue;
    }
}
