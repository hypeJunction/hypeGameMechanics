<?php

namespace hypeJunction\GameMechanics;

use Elgg\Hook;
use Elgg\Menu\MenuItems;
use ElggMenuItem;

class Menus {

	public static function setupEntityMenu(Hook $hook) {

		$entity = $hook->getEntityParam();

		if (!$entity instanceof Badge) {
			return null;
		}

		$menu = new MenuItems();

		if ($entity->canEdit()) {
			$menu[] = ElggMenuItem::factory([
				'name' => 'edit',
				'text' => elgg_echo('edit'),
				'title' => elgg_echo('edit:this'),
				'href' => elgg_generate_url('edit:object:hjbadge', ['guid' => $entity->guid]),
				'priority' => 200,
			]);

			$menu[] = ElggMenuItem::factory([
				'name' => 'delete',
				'text' => elgg_view_icon('delete'),
				'title' => elgg_echo('delete:this'),
				'href' => elgg_generate_action_url('entity/delete', [
					'guid' => $entity->guid,
				]),
				'confirm' => true,
				'priority' => 300,
			]);
		}

		if (!Reward::isClaimed($entity->guid) && Reward::isEligible($entity->guid)) {
			$menu[] = ElggMenuItem::factory([
				'name' => 'claim',
				'text' => elgg_echo('mechanics:claim'),
				'href' => elgg_generate_action_url('badge/claim', [
					'guid' => $entity->guid,
				]),
				'confirm' => ($entity->points_cost > 0) ? elgg_echo('mechanics:claim:confirm', [$entity->points_cost]) : false,
				'priority' => 400,
			]);
		}

		return $menu;
	}

	public static function setupOwnerBlockMenu(Hook $hook) {
		$entity = $hook->getEntityParam();

		if (!$entity instanceof \ElggUser) {
			return null;
		}

		$menu = $hook->getValue();

		if ($entity->canEdit()) {
			$badges = Policy::getBadges(['count' => true]);

			if ($badges) {
				$menu[] = ElggMenuItem::factory([
					'name' => 'badges',
					'text' => elgg_echo('mechanics:badges'),
					'href' => elgg_generate_url('points:owner', [
						'username' => $entity->username,
					]),
				]);
			}
		}

		return $menu;
	}

	public static function setupUserHoverMenu(Hook $hook) {
		$menu = $hook->getValue();

		$entity = $hook->getEntityParam();

		if (elgg_is_admin_logged_in()) {
			$menu[] = ElggMenuItem::factory([
				'name' => 'gm_reset',
				'text' => elgg_echo('mechanics:admin:reset'),
				'href' => elgg_generate_action_url('points/reset', [
					'user_guid' => $entity->guid,
				]),
				'icon' => 'fas fa-times',
				'confirm' => true,
				'section' => 'admin'
			]);
		}

		if ($entity->canAnnotate(0, 'gm_score_award')) {
			$menu[] = ElggMenuItem::factory([
				'name' => 'gm_score_award',
				'text' => elgg_echo('mechanics:admin:award'),
				'icon' => 'fas fa-coins',
				'link_class' => 'elgg-lightbox',
				'data-colorbox-opts' => json_encode([
					'href' => elgg_normalize_url("ajax/view/resources/points/award?guid=$entity->guid"),
					'maxWidth' => '600px',
				]),
			]);
		}

		return $menu;
	}

	public static function setupPageMenu(Hook $hook) {
		$menu = $hook->getValue();

		$menu[] = ElggMenuItem::factory([
			'name' => 'gamemechanics',
			'parent_name' => 'appearance',
			'text' => elgg_echo('mechanics:badges:site'),
			'href' => elgg_generate_url('collection:object:hjbadge:all'),
			'priority' => 500,
			'contexts' => ['admin'],
			'section' => 'configure'
		]);

		return $menu;
	}

	public static function setupSiteMenu(Hook $hook) {
		$menu = $hook->getValue();

		$menu[] = ElggMenuItem::factory([
			'name' => 'leaderboard',
			'text' => elgg_echo('mechanics:leaderboard'),
			'href' => elgg_generate_url('points:leaderboard'),
		]);

		return $menu;
	}
}
