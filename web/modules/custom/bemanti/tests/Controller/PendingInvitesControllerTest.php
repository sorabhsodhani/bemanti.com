<?php

namespace Drupal\bemanti\Tests;

use Drupal\simpletest\WebTestBase;

/**
 * Provides automated tests for the bemanti module.
 */
class PendingInvitesControllerTest extends WebTestBase {


  /**
   * {@inheritdoc}
   */
  public static function getInfo() {
    return [
      'name' => "bemanti PendingInvitesController's controller functionality",
      'description' => 'Test Unit for module bemanti and controller PendingInvitesController.',
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
   * Tests bemanti functionality.
   */
  public function testPendingInvitesController() {
    // Check that the basic functions of module bemanti.
    $this->assertEquals(TRUE, TRUE, 'Test Unit Generated via Drupal Console.');
  }

}
