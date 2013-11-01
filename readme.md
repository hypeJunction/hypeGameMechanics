hypeGameMechanics
=================

User points and game mechanics for Elgg


## Features

hypeGameMechanics allows your users to:
* Earn points actively for performing certain actions / activities on the site
* Earn points passively by receiving interactions on content items (e.g. ratings, comments, likes)
* Claim badges when a set of defined criteria are met

hypeGameMechanics can be integrated with most of third party plugins via hooks and handlers


## Getting Started

This plugin is governed by a set of 'rules' - conditions that describe an event when points should be awarded / deducted to the user
A set of rules can be extended / modified via hooks and handlers
Badges are described by 4 criteria:
	* A minimum a number of points the user should have
	* Up to 10 rule definitions with a number of recurrences for each action
	* A number of points a user should spend to uncover the badge
	* Other badges that are required before a badge can be claimed
