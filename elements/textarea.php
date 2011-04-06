<?php

namespace depage\htmlform\elements;

/**
 * HTML textarea element.
 **/
class textarea extends text {
    /**
     * @param $name input elements' name
     * @param $parameters array of input element parameters, HTML attributes, validator specs etc.
     * @param $formName name of the parent HTML form. Used to identify the element once it's rendered.
     **/
    public function __construct($name, $parameters, $form) {
        parent::__construct($name, $parameters, $form);

        $this->rows = (isset($parameters['rows'])) ? $parameters['rows'] : null;
        $this->cols = (isset($parameters['cols'])) ? $parameters['cols'] : null;
    }

    /**
     * Renders element to HTML.
     *
     * @return string of HTML rendered element
     **/
    public function __toString() {
        $label              = $this->htmlLabel();
        $marker             = $this->htmlMarker();
        $inputAttributes    = $this->htmlInputAttributes();
        $value              = $this->htmlValue();
        $rows               = $this->htmlRows();
        $cols               = $this->htmlCols();
        $wrapperAttributes  = $this->htmlWrapperAttributes();
        $errorMessage       = $this->htmlErrorMessage();

        return "<p {$wrapperAttributes}>" .
            "<label>" .
                "<span class=\"label\">{$label}{$marker}</span>" .
                "<textarea name=\"{$this->name}\"{$inputAttributes}{$rows}{$cols}>{$value}</textarea>" .
            "</label>" .
            $errorMessage .
        "</p>\n";
    }

    /**
     * Returns string of HTML rows attribute.
     **/
    protected function htmlRows() {
        return ($this->rows === null) ? "" : " rows=\"" . htmlentities($this->rows, ENT_QUOTES) . "\"";
    }

    /**
     * Returns string of HTML cols attribute.
     **/
    protected function htmlCols() {
        return ($this->cols === null) ? "" : " cols=\"" . htmlentities($this->cols, ENT_QUOTES) . "\"";
    }
}
