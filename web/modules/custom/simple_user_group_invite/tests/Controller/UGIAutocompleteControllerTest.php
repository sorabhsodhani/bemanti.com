<?php

namespace Drupal\simple_user_group_invite\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the simple_user_group_invite module.
 */
class UGIAutocompleteControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "simple_user_group_invite UGIAutocompleteController's controller functionality",
      'description' => 'Test Unit for module simple_user_group_invite and controller UGIAutocompleteController.',
      'group' => 'Other',
    ];
  }

  /**
   * {@inheritdoc}
   */
  public function setUp() {
    parent::setUp();
  }

  /**
   * Tests simple_user_group_invite functionality.
   */
  public function testUGIAutocompleteController() {
    // Check that the basic functions of module simple_user_group_invite.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
