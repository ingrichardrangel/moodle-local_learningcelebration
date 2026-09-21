@local @local_learningcelebration
Feature: Access Learning Celebration administration and learner pages
  In order to configure and use Learning Celebration safely
  As an administrator or authenticated learner
  I need the appropriate plugin pages to respect Moodle access controls

  Background:
    Given the following "users" exist:
      | username | firstname | lastname | email |
      | learner1 | Learning | Tester | learner1@example.invalid |

  Scenario: Site administrator can inspect configuration and preview pages
    Given I log in as "admin"
    When I am on "/local/learningcelebration/status.php"
    Then I should see "Configuration status"
    And I should see "Birthday engine simulator"
    When I am on "/local/learningcelebration/preview.php"
    Then I should see "Celebration preview"

  Scenario: Authenticated learner can access their Learning Celebration preferences
    Given I log in as "learner1"
    When I am on "/local/learningcelebration/preferences.php"
    Then I should see "Learning Celebration preferences"
    And I should see "Show my annual Learning Celebration automatically"

  Scenario: Guest access to learner preferences requires authentication
    Given I am on "/local/learningcelebration/preferences.php"
    Then I should see "Log in"
