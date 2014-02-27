<?php
/**
 *  Class used for escaping strings when outputting them.
 */
abstract class Escaper {

  /**
   *  Escapes the given string.
   *  
   *  @param  $string String
   *  @return Escaped string
   */
  abstract public function escape($string);

}
