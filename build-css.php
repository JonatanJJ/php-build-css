<?php

/**
 *  Build CSS
 *
 *  Function for generating CSS
 *
 *  @author Jonatan Jall Jahja
 *  @version 1.3
 *
 *  @param array|string $attr {
 *    Array or string representing CSS or nested selectors 
 *
 *    @type string  CSS string
 *    
 *    @type array   Array of CSS attribute => valuye pairs, or nested selector => attributes.
 *
 *                  If the key contains the symbol "&", and $selector is not empty, it will be interpreted as a selector
 *                  and the value will be interpreted as an $attr.
 *                  Any "&" in the nested selector will be replaced with the parent selector, like in LESS.
 *
 *                  If the above case is not true the key will be interpreted as CSS attribute
 *                  and the value will be interpreted as one or more CSS values.
 *
 *                  If multiple CSS values are provided for one attribute the attribute will be duplicated.
 *                  Useful for example when providing a fallback for a linear-gradient.
 *                  If a CSS value is FALSE the attribute and value is discarded.
 *
 *  }
 *
 *  @param string $selector   A valid CSS selector, or empty string if CSS should be returned without a selector
 *                            for example when used within a HTML style-attribute.
 *
 *  @param bool $minified     If set to true, will not output tabs or line breaks.
 *  
 *  @return Valid CSS string that can be included between <style> tags, or within a style attribute
 *          The return value should be properly escaped if to be used within a style attribute.
 *
 */
function build_css($attrs, $selector = '', $minified = false){
  $css = '';

  $tab = $minified ? '' : "\t";
  $nl = $minified ? '' : "\n";

  $nested = array();
  if(is_array($attrs)){
    foreach ($attrs as $attr => $values) {
      $attr = trim($attr);

      // Check for nested selectors, i.e &:hover
      if(strpos($attr, '&') !== false){
        $nested[$attr] = $values;
        continue;
      }

      // $values can be an array to allow for fallbacks
      $values = (array)$values;
      foreach ($values as $value) {
        if($value === false) continue;

        $css .= $tab.$attr . ': ' . $value . ';'.$nl;
      }
    }
  }else{
    $css .= $tab.$attrs.$nl;
  }

  if(!empty($selector)){
    if(!empty($css)){
      $css = $selector . '{'.$nl.$css.'}'.$nl;
    }

    if(!empty($nested)){
      // Output nested selectors
      $parent_selectors = array_filter(array_map('trim', explode(',', $selector)));
      foreach ($nested as $nested_selector => $nested_attrs) {
        // Build nested selector
        $nested_selectors = array();
        foreach ($parent_selectors as $parent_selector) {
          $nested_selectors[] = str_replace('&', $parent_selector, $nested_selector);
        }

        $css .= build_css($nested_attrs,  implode(','.$nl, $nested_selectors));
      }
    }
  }

  // Only a closing style tag can lead to XSS
  // https://stackoverflow.com/questions/3720836/escaping-javascript-css-between-script-style-tags-insights-on-a-potenti
  return str_replace('</style', '&lt;/style', $css);
}

/**
 *  Build CSS attribute
 *
 *  Function for generating CSS for use inside a style attribute
 *
 *  @author Jonatan Jall Jahja
 *  @version 1.0
 *
 *  @param array|string $attr @see build_css
 *  
 *  @return Valid CSS string that can be safely included within a style attribute.
 *
 */
function build_css_attr($attrs){
  return htmlspecialchars(build_css($attrs,'', true), ENT_QUOTES );
}