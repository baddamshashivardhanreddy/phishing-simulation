<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

$routes->get('/', 'Admin::index');

// Auth Routes
$routes->match(['get', 'post'], 'login', 'Auth::login');
$routes->get('logout', 'Auth::logout');

// Phishing Simulation Tracking Routes (supports optional campaign_id and image extensions)
$routes->get('image/pixel.gif', 'Phish::image');
$routes->get('image/pixel.png', 'Phish::image');
$routes->get('image/(:segment)/(:num)/pixel.gif', 'Phish::image/$1/$2');
$routes->get('image/(:segment)/(:num)/pixel.png', 'Phish::image/$1/$2');
$routes->get('image/(:segment)/(:num)', 'Phish::image/$1/$2');
$routes->get('image/(:segment)', 'Phish::image/$1');
$routes->get('image', 'Phish::image');

$routes->get('download/(:segment)/(:num)', 'Phish::download/$1/$2');
$routes->get('download/(:segment)', 'Phish::download/$1');
$routes->get('download', 'Phish::download');

$routes->get('training/(:segment)/(:num)', 'Phish::training/$1/$2');
$routes->get('training/(:segment)', 'Phish::training/$1');
$routes->get('training', 'Phish::training');
$routes->post('training/submit', 'Phish::submit_form');
$routes->post('training', 'Phish::submit_form');

$routes->get('warning/(:segment)/(:num)', 'Phish::warning/$1/$2');
$routes->get('warning/(:segment)', 'Phish::warning/$1');
$routes->get('warning', 'Phish::warning');
$routes->get('lock', 'Phish::lockscreen');

// Phishing Reporting Routes (Gophish-style "Report Phish")
$routes->get('report/(:segment)/(:num)', 'Phish::report/$1/$2');
$routes->get('report/(:segment)', 'Phish::report/$1');
$routes->get('report', 'Phish::report');
$routes->get('admin/mark_reported/(:num)', 'Admin::mark_reported/$1');
$routes->get('admin/mark_opened/(:num)', 'Admin::mark_opened/$1');

// Campaign Routes (Gophish-style multi-campaign management)
$routes->get('admin', 'Admin::index');
$routes->get('admin/campaigns', 'Admin::campaigns');
$routes->get('admin/campaigns/view/(:num)', 'Admin::view_campaign/$1');
$routes->get('admin/campaigns/complete/(:num)', 'Admin::complete_campaign/$1');
$routes->get('admin/campaigns/delete/(:num)', 'Admin::delete_campaign/$1');
$routes->get('admin/export', 'Admin::export');
$routes->post('admin/send', 'Admin::send_simulation');
$routes->get('admin/settings', 'Admin::settings');
$routes->post('admin/save_settings', 'Admin::save_settings');
$routes->get('admin/clear_logs', 'Admin::clear_logs');

// Simulation Users / Targets Management (Independent Directory)
$routes->get('admin/users', 'Admin::users');
$routes->post('admin/users/save', 'Admin::save_user');
$routes->get('admin/users/delete/(:num)', 'Admin::delete_user/$1');
$routes->post('admin/users/bulk_delete', 'Admin::bulk_delete_users');
$routes->post('admin/import_targets', 'Admin::import_targets');
$routes->get('admin/sample_csv', 'Admin::sample_csv');
$routes->get('admin/delete_target/(:num)', 'Admin::delete_target/$1');
$routes->post('admin/bulk_delete_campaign_targets', 'Admin::bulk_delete_campaign_targets');
$routes->get('admin/clear_targets', 'Admin::clear_targets');

// Email Templates (Gophish-style)
$routes->get('admin/templates', 'Admin::templates');
$routes->post('admin/save_template', 'Admin::save_template');
$routes->get('admin/delete_template/(:num)', 'Admin::delete_template/$1');
$routes->get('admin/template_preview/(:num)', 'Admin::template_preview/$1');

// Admin Users Management & Profile
$routes->get('admin/profile', 'Admin::profile');
$routes->post('admin/update_profile', 'Admin::update_profile');
$routes->post('admin/create_admin', 'Admin::create_admin');
$routes->get('admin/delete_admin/(:num)', 'Admin::delete_admin/$1');

// Quiz Routes
$routes->get('quiz', 'Quiz::index');
$routes->post('quiz/submit', 'Quiz::submit');
