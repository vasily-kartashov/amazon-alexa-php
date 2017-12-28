<?php

namespace Alexa\Request;

class IntentRequest extends Request
{
    /** @var string */
    public $intentName;

    /**
     * @var array
     * @psalm-var array<string,mixed>
     */
    public $slots = [];

    /**
     * @param string $rawData
     */
    public function __construct($rawData)
    {
        parent::__construct($rawData);
        $data = $this->data;

        $this->intentName = $data['request']['intent']['name'];

        if (isset($data['request']['intent']['slots'])) {
            foreach ($data['request']['intent']['slots'] as $slot) {
                if (isset($slot['value'])) {
                    $name = (string) $slot['name'];
                    $this->slots[$name] = $slot['value'];
                }
            }
        }
    }


    /**
     * Returns the value for the requested intent slot, or $default if not found.
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function getSlot($name, $default = false)
    {
        if (array_key_exists($name, $this->slots)) {
            return $this->slots[$name];
        } else {
            return $default;
        }
    }
}
