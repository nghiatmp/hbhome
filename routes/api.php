<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group([
    'namespace' => 'API',
], function () {
    Route::group([
        'namespace' => 'Auth',
        'prefix'    => 'auth',
    ], function () {
        Route::post('/login', 'AuthController@login');
        Route::get('refresh', 'AuthController@refresh');
        Route::group(['middleware' => 'auth:api'], function () {
            Route::get('user', 'AuthController@user');
            Route::post('logout', 'AuthController@logout');
        });
    });

    Route::group([
        'namespace' => 'Auth',
        'prefix'    => 'auth',
        'middleware' => ['auth.jwt', 'systemRole:MEMBER|LEADER|ADMIN'],
    ], function () {
        Route::get('/me', 'MeController@main');
    });

    Route::group([
        'namespace' => 'Users',
        'prefix'    => 'users',
    ], function () {
        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN'],
        ], function () {
            Route::get('', 'IndexController@main');
            Route::get('search', 'SearchController@main');
            Route::get('email-exists', 'EmailExistsController@main');
            Route::post('', 'StoreController@main');
        });

        Route::group([
//            'middleware' => ['auth.jwt', 'systemRole:ADMIN', 'checkUser']
        ], function () {
            Route::put('{userId}', 'UpdateController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|LEADER|MEMBER'],
        ], function () {
            Route::get('suggest', 'SuggestController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|LEADER|MEMBER', 'checkUser']
        ], function () {
            Route::get('{userId}/resources', 'IndexResourceController@main');
            Route::get('{userId}/busy-rates', 'BusyRateController@main');
            Route::get('{userId}/effort', 'EffortController@main');
        });
    });

    Route::group([
        'namespace' => 'Teams',
        'prefix'    => 'teams',
    ], function () {
        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN'],
        ], function () {
            Route::post('', 'StoreController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|LEADER|MEMBER'],
        ], function () {
            Route::get('', 'IndexController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|LEADER', 'checkTeam'],
        ], function () {
            Route::get('{teamId}/members', 'IndexMemberController@main');
            Route::get('{teamId}/busy-rates', 'BusyRateController@main');
            Route::get('{teamId}/effort', 'EffortController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN', 'checkTeam'],
        ], function () {
            Route::delete('{teamId}', 'DestroyController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|LEADER|MEMBER', 'checkTeam', 'teamRole:ADMIN'],
        ], function () {
            Route::put('{teamId}', 'UpdateController@main');
            Route::post('{teamId}/members', 'StoreMemberController@main');
        });
    });

    Route::group([
        'namespace' => 'TeamMembers',
        'prefix'    => 'team-members',
        'middleware' => ['auth.jwt', 'systemRole:ADMIN|LEADER|MEMBER', 'checkTeamMember', 'teamRole:ADMIN'],
    ], function () {
        Route::put('{teamMemberId}', 'UpdateController@main');
        Route::delete('{teamMemberId}', 'DestroyController@main');
    });

    Route::group([
        'namespace' => 'Divisions',
        'prefix'    => 'divisions',
    ], function () {
        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN'],
        ], function () {
            Route::post('', 'StoreController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|MEMBER|LEADER'],
        ], function () {
            Route::get('', 'IndexController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN', 'checkDivision'],
        ], function () {
            Route::put('{divisionId}', 'UpdateController@main');
        });
    });

    Route::group([
        'namespace' => 'Projects',
        'prefix'    => 'projects',
    ], function () {
        Route::group([
            'middleware' => ['auth.jwt'],
        ], function () {
            Route::get('key-exists', 'KeyExistsController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN'],
        ], function () {
            Route::get('', 'IndexController@main');
            Route::post('', 'StoreController@main');
            Route::get('search', 'SearchController@main');
        });
        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|LEADER'],
        ], function () {
            Route::get('/all', 'IndexController@all');
            Route::get('/all/me', 'MyIndexController@all');
        });
        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|MEMBER|LEADER'],
        ], function () {
            Route::get('me', 'MyIndexController@main');
            Route::get('search/me', 'MySearchController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|MEMBER|LEADER', 'checkProject', 'projectRole:ADMIN|MEMBER'],
        ], function () {
            Route::get('{projectId}', 'ShowController@main');
            Route::get('{projectId}/ee', 'EEController@main');
            Route::get('{projectId}/effort', 'EffortController@main');
            Route::get('{projectId}/members', 'IndexMemberController@main');
            Route::get('{projectId}/resources', 'IndexResourceController@main');
            Route::get('{projectId}/phases', 'IndexPhaseController@main');
            Route::get('{projectId}/effort-members', 'EffortMemberController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|MEMBER|LEADER', 'checkProject', 'projectRole:ADMIN'],
        ], function () {
            Route::put('{projectId}', 'UpdateController@main');
            Route::post('{projectId}/members', 'StoreMemberController@main');
            Route::post('{projectId}/resources', 'StoreResourceController@main');
            Route::post('{projectId}/phases', 'StorePhaseController@main');
        });
    });

    Route::group([
        'namespace' => 'Resources',
        'prefix'    => 'resources',
        'middleware' => ['auth.jwt', 'systemRole:ADMIN|MEMBER|LEADER', 'checkResource', 'projectRole:ADMIN'],
    ], function () {
        Route::put('{resourceId}', 'UpdateController@main');
        Route::delete('{resourceId}', 'DestroyController@main');
    });

    Route::group([
        'namespace' => 'Phases',
        'prefix'    => 'phases',
    ], function () {
        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN'],
        ], function () {
            Route::get('search', 'SearchController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN', 'checkPhase'],
        ], function () {
            Route::delete('{phaseId}', 'DestroyController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|MEMBER|LEADER'],
        ], function () {
            Route::get('search/me', 'MySearchController@main');
        });

        Route::group([
            'middleware' => ['auth.jwt', 'systemRole:ADMIN|MEMBER|LEADER', 'checkPhase', 'projectRole:ADMIN'],
        ], function () {
            Route::get('{phaseId}', 'ShowController@main');
            Route::put('{phaseId}', 'UpdateController@main');
            Route::put('{phaseId}/change-status', 'ChangeStatusController@main');
        });
    });

    Route::group([
        'namespace' => 'ProjectMembers',
        'prefix'    => 'project-members',
        'middleware' => ['auth.jwt', 'systemRole:ADMIN|MEMBER|LEADER', 'checkProjectMember', 'projectRole:ADMIN'],
    ], function () {
        Route::put('{projectMemberId}', 'UpdateController@main');
        Route::put('{projectMemberId}/change-status', 'ChangeStatusController@main');
    });

    Route::group([
        'namespace' => 'HideSetting',
        'prefix'    => 'hide-setting',
        'middleware' => ['auth.jwt', 'systemRole:ADMIN|MEMBER|LEADER'],
    ], function () {
        Route::get('', 'IndexController@main');
        Route::post('', 'StoreController@main');
    });

    Route::group([
        'namespace' => 'Holidays',
        'prefix'    => 'holidays',
        'middleware' => ['auth.jwt', 'systemRole:ADMIN|MEMBER|LEADER'],
    ], function () {
        Route::get('', 'IndexController@main');
        Route::post('', 'StoreController@main');
        Route::put('{holidayId}', 'UpdateController@main');
        Route::delete('{holidayId}', 'DestroyController@main');
    });

    Route::group([
        'namespace' => 'ActivityLogs',
        'prefix'    => 'activity-logs',
        'middleware' => ['auth.jwt', 'systemRole:ADMIN|LEADER'],
    ], function () {
        Route::get('/', 'IndexController@main');
    });

    Route::group([
        'namespace' => 'Overview',
        'prefix'    => 'overview',
        'middleware' => ['auth.jwt', 'systemRole:ADMIN'],
    ], function () {
        Route::get('ee', 'EEController@main');
        Route::get('mm', 'MMController@main');
    });
});
