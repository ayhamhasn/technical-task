@coordinates
Feature: Coordinates feature
  In order to ensure the organization of Coordinates
  As an authenticated API client
  I need to be able to manage coordinates using API methods

  Scenario: Get existing Coordinates
    Given the database is empty
    When I request "GET /coordinates?countryCode=DE&city=berlin&street=ritterlandweg+26&postcode=13409"
    Then the response status code should be 200
    And I see the json response:
      """
		{
		    "lat": 52.560531,
		    "lng": 13.3724414
		}
      """
    And the response "lat" is "52.560531"
    And the response "lng" is "13.3724414"