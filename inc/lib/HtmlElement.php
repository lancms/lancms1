<?php

/**
 * Class HtmlElement
 *
 * @author edvin
 */
class HtmlElement {

    protected $_contents;
    protected $_attributes;
    protected $_elementTag;

    /**
     * Construct a new html element.
     *
     * @param string $tag The tag name of this element, make sure to remove any whitespace!
     * @param array|mixed $contents Add content to this element at once.
     */
    function __construct($tag, $contents=array()) {
        $this->_elementTag = $tag;
        $this->_attributes = array();
        $this->_contents = array();

        $this->addContent($contents);
    }

    /**
     * Provides all attributes of this element.
     *
     * @return array
     */
    public function getAttributes() {
        return $this->_attributes;
    }

    /**
     * Provides a value of the requested attribute name. Returns null if the attribute is not set.
     *
     * @param string $name
     * @return string|null
     */
    public function getAttribute($name) {
        $attributes = $this->getAttributes();
        return (isset($attributes[$name]) ? $attributes[$name] : null);
    }

    /**
     * Set a new attribute on this element with name and value.
     *
     * @param string $name
     * @param string $value
     * @return HtmlElement
     */
    public function setAttribute($name, $value) {
        $this->_attributes[$name] = $value;
        return $this;
    }

    /**
     * Provides a single string representation of the attributes on this
     * element.
     *
     * @return string
     */
    public function getAttributesString() {
        $attributes = array();

        if (count($this->getAttributes()) > 0) {
            foreach ($this->getAttributes() as $name=>$attribute) {
                $attributes[] = $name . "=\"" . $attribute . "\"";
            }
        }

        return implode(" ", $attributes);
    }

    /**
     * Add content to this element, the input can be array or a single instance of an object
     * that has implemented __toString()
     *
     * @param array|mixed $contents
     * @return HtmlElement Returns this element.
     */
    public function addContent($contents=array()) {
        $contents = (is_array($contents) ? $contents : array($contents));

        foreach ($contents as $content) {
            $this->_contents[] = $content;
        }

        return $this;
    }

    /**
     * Add a new element by tag to this element and return the new object.
     *
     * @param string $tagName The new element's tag name.
     * @return HtmlElement The created element.
     */
    public function addElement($tagName, $contents=array()) {
        $element = new HtmlElement($tagName, $contents);
        $this->addContent($element);

        return $element;
    }

    /**
     * Add an array of css classes to this element by using addCssClass()
     *
     * @param array $classes
     * @return HtmlElement
     */
    public function addCssClasses($classes) {
        if (is_array($classes) == false || count($classes) < 1)
            return;

        foreach ($classes as $class) {
            $this->addCssClass($class);
        }

        return $this;
    }

    /**
     * Add a css class to this element by using the attribute methods.
     * If the provided class is already a class, it's not added.
     *
     * @param string $class The new class to add.
     * @return HtmlElement
     */
    public function addCssClass($class) {
        $cssClasses = explode(" ", $this->getAttribute("class"));

        // Create the cssClasses as an array
        if (is_array($cssClasses) == false) {
            $cssClasses = array();
        }

        if (in_array($class, $cssClasses) == false) {
            $cssClasses[] = $class;
            $this->setAttribute("class", trim(implode(" ", $cssClasses)));
        }

        return $this;
    }

    /**
     * Indicates if the tag for this element has a closing tag.
     * Source: http://webdesign.about.com/od/htmltags/qt/html-void-elements.htm
     *
     * @return string
     */
    public function hasClosingTag() {
        return in_array($this->_elementTag, array(
            "area",
            "base",
            "br",
            "col",
            "command",
            "embed",
            "hr",
            "img",
            "input",
            "link",
            "meta",
            "param",
            "source"
        ));
    }

    /**
     * Provides this elements html.
     *
     * @return string
     */
    public function asHtml() {
        $attributeString = "";
        if (count($this->getAttributes()) > 0) {
            $attributeString = " " . $this->getAttributesString();
        }

        $html = PHP_EOL . "\t<" . $this->_elementTag . $attributeString . "";

        if ($this->hasClosingTag() == true) {
            $html .= " />";
        } else {
            $html .= ">" . implode(" ", $this->_contents) . PHP_EOL . "</" . $this->_elementTag . ">";
        }

        return $html;
    }

    /**
     * Provides this elements html.
     *
     * @return string
     */
    public function __toString() {
        return $this->asHtml();
    }

}
