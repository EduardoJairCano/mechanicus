<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Inertia\Response;
use Jenssegers\Agent\Agent;
use Laravel\Jetstream\Jetstream;

class UserController extends Controller
{
    /**
     * Show the general profile settings screen.
     *
     * @param Request $request
     * @return Response
     */
    public function show(Request $request): Response
    {
        return Jetstream::inertia()->render($request, 'Profile/Show', [
            'sessions' => $this->sessions($request)->all(),
            'userInfo' => $this->getUserInfo($request),
        ]);
    }

    /**
     * Get the current sessions.
     *
     * @param Request $request
     * @return Collection
     */
    public function sessions(Request $request): Collection
    {
        if (config('session.driver') !== 'database') {
            return collect();
        }

        return collect(
            DB::connection(config('session.connection'))->table(config('session.table', 'sessions'))
                ->where('user_id', $request->user()->getAuthIdentifier())
                ->orderBy('last_activity', 'desc')
                ->get()
        )->map(function ($session) use ($request) {
            $agent = $this->createAgent($session);

            return (object) [
                'agent' => [
                    'is_desktop' => $agent->isDesktop(),
                    'platform' => $agent->platform(),
                    'browser' => $agent->browser(),
                ],
                'ip_address' => $session->ip_address,
                'is_current_device' => $session->id === $request->session()->getId(),
                'last_active' => Carbon::createFromTimestamp($session->last_activity)->diffForHumans(),
            ];
        });
    }

    /**
     * Create a new agent instance from the given session.
     *
     * @param  mixed  $session
     * @return Agent
     */
    protected function createAgent($session): Agent
    {
        return tap(new Agent, static function ($agent) use ($session) {
            $agent->setUserAgent($session->user_agent);
        });
    }

    /**
     * Get the userInfo resource.
     *
     * @param Request $request
     * @return Model|Builder|object|null
     */
    protected function getUserInfo(Request $request)
    {
        return DB::connection(config('session.connection'))
            ->table('users_info')
            ->where('user_id', $request->user()->getAuthIdentifier())
            ->first();
    }
}
