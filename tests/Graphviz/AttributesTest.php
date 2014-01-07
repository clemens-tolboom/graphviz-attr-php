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

  function testAttributes() {
    $attributes = Attributes::getAttributes();

    $this->assertArrayHasKey("arrowhead", $attributes, "arrowhead key exists");
    $this->assertArrayHasKey("URL", $attributes, "URL key exists");
    $this->assertArrayHasKey("dir", $attributes, "dir key exists");
  }

}
