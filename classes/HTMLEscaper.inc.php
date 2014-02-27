<?php
/**
 *  Class used for escaping for HTML.
 */
class HTMLEscaper extends Escaper {

  /**
   *  Escapes the given string.
   *  
   *  @param  $string String
   *  @return Escaped string
   */
  public function escape($string) {
    return htmlspecialchars($string);
  }

}
