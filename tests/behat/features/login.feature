Feature: ResourceSpace
  In order to use RS
  As a website user
  I need to be able to log in

  Scenario: wrong password
  Given I am on "/login.php"
    When I fill in "Username" with "admin"
    And I fill in "password" with "admin"
    And I press "Log in"
    Then I should see "Sorry, your login details were incorrect."

  Scenario: happy path
  Given I am on "/login.php"
    When I fill in "Username" with "admin"
    And I fill in "password" with "jbb123new"
    And I press "Log in"
    Then I should see "Your introductory text here."


