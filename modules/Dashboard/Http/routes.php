<?php
Route::group(['prefix' => CMS::backendPath(), 'as' => 'backend.'], function ()
{
	Route::get('/', ['as' => 'dashboard', 'uses' => 'DashboardController@getIndex']);
});

Route::group(['as' => 'api.dashboard.widget.'], function ()
{
	RouteAPI::get('dashboard.widget.list', ['as' => 'list', 'uses' => 'DashboardController@getWidgetList']);
	RouteAPI::get('dashboard.widget', ['as' => 'settings', 'uses' => 'API\DashboardController@getWidgetSettings']);
	RouteAPI::put('dashboard.widget', ['as' => 'put', 'uses' => 'API\DashboardController@putWidget']);
	RouteAPI::post('dashboard.widget', ['as' => 'post', 'uses' => 'API\DashboardController@postWidget']);
	RouteAPI::delete('dashboard.widget', ['as' => 'delete', 'uses' => 'API\DashboardController@deleteWidget']);
});