<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use DB;
use Auth;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function index(Request $request) {
      return view('index');
    }

    public static function logged_in() {
      $user = Auth::user();
      $org = DB::table('orgs')->where('id', $user->org_id)->first();
      return [$user, $org];
    }

    public static function load_user_groups($user) {
      $groups = DB::table('groups')->where('org_id', $user->org_id)->get();
      $subscriptions = DB::table('subscriptions')->where('user_id', $user->id)->lists('group_id');

      $my_groups = array_filter($groups, function($g) use($subscriptions) {
        return in_array($g->id, $subscriptions);
      });
      $other_groups = array_filter($groups, function($g) use($subscriptions) {
        return !in_array($g->id, $subscriptions);
      });

      return [$my_groups, $other_groups];
    }

    public function dashboard(Request $request) {
      list($who, $org) = self::logged_in();

      list($my_groups, $other_groups) = self::load_user_groups($who);

      return view('dashboard', [
        'org' => $org,
        'user' => $who,
        'my_groups' => $my_groups,
        'other_groups' => $other_groups
      ]);
    }

    public function user_profile(Request $request) {
      list($who, $org) = self::logged_in();

      $user = DB::table('users')->where('org_id', $who->org_id)->where('username', $request->username)->first();
      if(!$user) {
        return 'not found';
      }

      list($my_groups, $other_groups) = self::load_user_groups($user);

      $entries = DB::table('entries')
        ->select('entries.*', 'groups.shortname AS groupname', 'users.username', 'users.display_name', 'users.photo_url', 'users.timezone')
        ->join('groups', 'entries.group_id','=','groups.id')
        ->join('users', 'entries.user_id','=','users.id')
        ->where('entries.user_id', $user->id)
        ->orderBy('entries.created_at', 'desc')
        ->limit(30)->get();

      return view('profile', [
        'org' => $org,
        'user' => $user,
        'my_groups' => $my_groups,
        'entries' => $entries
      ]);
    }

    public function group_profile(Request $request) {
      list($who, $org) = self::logged_in();

      $group = DB::table('groups')->where('org_id', $who->org_id)->where('shortname', $request->group)->first();
      if(!$group) {
        return 'not found';
      }

      $subscribers = DB::table('users')
        ->join('subscriptions', 'users.id','=','subscriptions.user_id')
        ->where('subscriptions.group_id', $group->id)
        ->orderBy('users.username', 'asc')
        ->get();

      return view('group', [
        'org' => $org,
        'group' => $group,
        'subscribers' => $subscribers,
      ]);
    }

    public function logout(Request $request) {
      Auth::logout();
      return redirect('/');
    }
}
