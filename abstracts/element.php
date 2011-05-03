<?php
/**
 * @file    element.php
 * @brief   element class
 **/

namespace depage\htmlform\abstracts;

use depage\htmlform\exceptions;

/**
 * @brief behold: the über-class
 *
 * The abstract element class contains the basic attributes and tools of
 * container and input elements.
 **/
abstract class element {
    /**
     * @brief que?
     *
     * Element name.
     **/
    protected $name;
    /**
     * Contains element validation status/result.
     **/
    protected $valid;
    /**
     * True if the element has been validated before.
     **/
    protected $validated = false;
    /**
     * Log object reference
     **/
    protected $log;

    /**
     * @brief   element class constructor
     *
     * @param   $name       (string)    element name
     * @param   $parameters (array)     element parameters, HTML attributes
     * @param   $form       (object)    parent form object reference
     * @return  void
     **/
    public function __construct($name, $parameters, $form) {
        $this->checkName($name);
        $this->checkParameters($parameters);

        $this->name = $name;

        $this->setDefaults();
        $parameters = array_change_key_case($parameters);
        foreach ($this->defaults as $parameter => $default) {
            $this->$parameter = isset($parameters[strtolower($parameter)]) ? $parameters[strtolower($parameter)] : $default;
        }
    }

    /**
     * @brief   Collects initial values across subclasses.
     *
     * @return  void
     **/
    protected function setDefaults() {
        $this->defaults['log'] = null;
    }

    /**
     * @brief   HTML escaping
     *
     * Returns respective HTML escaped attributes for element rendering.
     *
     * @param   $function   (string)    function name
     * @param   $arguments  (array)     function arguments
     * @return              (mixed)     HTML escaped value
     *
     * @see     htmlEscape()
     **/
    public function __call($function, $arguments) {
        if (substr($function, 0, 4) === 'html') {
            $attribute = str_replace('html', '', $function);
            $attribute{0} = strtolower($attribute{0});

            return $this->htmlEscape($this->$attribute);
        } else {
            trigger_error("Call to undefined method $function", E_USER_ERROR);
        }
    }

    /**
     * @brief   Returns the element name.
     *
     * @return  $this->name (string) element name
     **/
    public function getName() {
        return $this->name;
    }

    /**
     * @brief   checks element parameters
     *
     * Throws an exception if $parameters isn't of type array.
     *
     * @param   $parameters (array) parameters for element constructor
     * @return  void
     **/
    protected function checkParameters($parameters) {
        if ((isset($parameters)) && (!is_array($parameters))) {
            throw new exceptions\elementParametersNoArrayException('Element "' . $this->getName() . '": parameters must be of type array.');
        }
    }

    /**
     * @brief   checks for valid element name
     *
     * Checks that element name is of type string, not empty and doesn't
     * contain invalid characters. Otherwise throws an exception.
     *
     * @param   $name (string) element name
     * @return  void
     **/
    private function checkName($name) {
        if (
            !is_string($name)
            || trim($name) === ''
            || preg_match('/[^a-zA-Z0-9_]/', $name)
        )  {
            throw new exceptions\invalidElementNameException('"' . $name . '" is not a valid element name.');
        }
    }

    /**
     * @brief error & warning logger
     *
     * If the element is constructed with a custom log object the logging
     * happens there, otherwise the PHP error_log function is used.
     *
     * @param   $argument (string) message
     * @param   $type     (string) type of log message
     * @return  void
     **/
    protected function log($argument, $type = null) {
        if (is_callable(array($this->log, 'log'))) {
            $this->log->log($argument, $type);
        } else {
            error_log($argument);
        }
    }

    /**
     * @brief   Escapes HTML in strings and arrays of strings
     *
     * @param   $options        (mixed) value to be HTML-escaped
     * @return  $htmlOptions    (mixed) HTML escaped value
     *
     * @see     __call()
     **/
    protected function htmlEscape($options = array()) {
        if (is_string($options)) {
            $htmlOptions = htmlspecialchars($options);
        } elseif (is_array($options)) {
            $htmlOptions = array();

            foreach($options as $index => $option) {
                if (is_string($index))  $index  = htmlspecialchars($index, ENT_QUOTES);
                if (is_string($option)) $option = htmlspecialchars($option, ENT_QUOTES);

                $htmlOptions[$index] = $option;
            }
        } else {
            $htmlOptions = $options;
        }
        return $htmlOptions;
    }
}
