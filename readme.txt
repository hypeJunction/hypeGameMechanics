hypeGameMechanics is part of the hypeJunction plugin bundle

This plugin is released under a GPL compatible license, as a courtesy of hypeJunction Club
Release of this plugin available at elgg.org might not always correspond to the latest stable release available at www.hypeJunction.com


PLUGIN DESCRIPTION
------------------
hypeGameMechanics is an attempt to create a comprehensive all-in-one tool for enabling game mechanics in Elgg sites.

hypeGameMechanics allows your users to:
-- Earn points actively for performing certain actions / activities on the site
-- Earn points passively by receiving interactions on content items (e.g. ratings, comments, likes)
-- Claim badges when a set of defined criteria are met

Soon to be added:
-- Support for gifts
-- Support for footprints

REQUIREMENTS
------------
1) Elgg 1.8.3+
2) hypeFramework 1.9+

INTEGRATION / COMPATIBILITY
---------------------------
-- hypeGameMechanics can be integrated with most of third party plugins via hooks and handlers

INSTALLATION
------------

USER GUIDE
----------
POINTS:
-- This plugin is governed by a set of 'rules' - conditions that describe an event when points should be awarded / deducted to the user
-- A set of rules can be overridden via hooks and handlers

BADGES:
-- Badges are described by 4 criteria:
	-- A minimum a number of points the user should have
	-- Up to 10 rule definitions with a number of recurrences for each action
	-- A number of points a user should spend to uncover the badge
	-- Other badges that are required before a badge can be claimed
-- Due to peformance issues, badges will not be added automatically, rather a user should claim them. This makes additional sense, considering it should be a decision of the user whether to spend points to uncover the badge
-- Only 'rules' with points assigned to them will be available for badge criteria selection


TODO
----
-- Add support for gifts
-- Add support for footprints
-- Add daily recurring max for each action
-- Add cron triggers for surprise badges
-- Add leaderboard widget

NOTES / WARNINGS
----------------
-- For now, I discourage the use of this plugin on production sites. It might slow things down as I am still trying to nail down some of the performance issues


BUG REPORTS
-----------
Bugs and feature requests can be submitted at:
http://hypeJunction.com/trac

