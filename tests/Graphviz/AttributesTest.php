<?php

use Graphviz\Attributes;

class AttributesTest extends TestCase {
  function testSanity() {
    $this->assertTrue(true, "Test runner ok");
  }
  
  function testTypes() {
    $types = Attributes::getTypes();
    
    $this->assertArrayHasKey("G", $types, "Graph key exists");
  }
}