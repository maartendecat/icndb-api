<?php
/**
 *  Class used for escaping for JavaScript.
 */
class JavaScriptEscaper extends Escaper {

  /**
   *  Escapes the given string.
   *  
   *  @param  $string String
   *  @return Escaped string
   */
  public function escape($string) {
    // only escape double quotes
    return str_replace("\"", "\\\"", $string);
  }

}
