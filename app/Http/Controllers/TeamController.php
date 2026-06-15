<?php

namespace App\Http\Controllers;

use App\Models\TeamPost;
use Illuminate\Http\Request;

class TeamController extends Controller
{
    private array $roles = [
        'assistant_owner' => 'Assistant Owner',
        'country_manager' => 'Country Manager',
        'super_admin' => 'Super Admin',
        'bd' => 'BD',
        'agency' => 'Agency',
        'host' => 'Host',
    ];

    private array $parentMap = [
        'country_manager' => 'assistant_owner',
        'super_admin' => 'country_manager',
        'bd' => 'super_admin',
        'agency' => 'bd',
        'host' => 'agency',
    ];

    private array $icons = [
        'assistant_owner' => '👑',
        'country_manager' => '🌍',
        'super_admin' => '⭐',
        'bd' => '💼',
        'agency' => '🏢',
        'host' => '🎙️',
    ];

    public function index($role)
    {
        abort_if(!isset($this->roles[$role]), 404);

        $posts = TeamPost::where('role', $role)->latest()->get();

        return view('admin.team.index', [
            'posts' => $posts,
            'role' => $role,
            'roleName' => $this->roles[$role],
        ]);
    }

    public function create($role)
    {
        abort_if(!isset($this->roles[$role]), 404);

        $parents = collect();

        if (isset($this->parentMap[$role])) {
            $parents = TeamPost::where('role', $this->parentMap[$role])
                ->where('status', 'active')
                ->get();
        }

        return view('admin.team.create', [
            'role' => $role,
            'roleName' => $this->roles[$role],
            'parents' => $parents,
        ]);
    }

    public function store(Request $request, $role)
    {
        abort_if(!isset($this->roles[$role]), 404);

        $request->validate([
            'real_id' => 'required|digits:6',
            'name' => 'required|string',
            'phone' => 'required|string',
            'email' => 'nullable|email',
            'country' => 'nullable|string',
            'parent_post_id' => isset($this->parentMap[$role]) ? 'required|string' : 'nullable',
        ]);

        $duplicate = TeamPost::where('real_id', $request->real_id)
            ->where('role', $role)
            ->where('status', 'active')
            ->exists();

        if ($duplicate) {
            return back()->withErrors([
                'real_id' => 'Is Real ID par ye post already active hai.'
            ])->withInput();
        }

        $postId = $this->generatePostId($role);

        $data = [
            'real_id' => $request->real_id,
            'post_id' => $postId,
            'parent_post_id' => $request->parent_post_id,

            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'role' => $role,
            'badge_name' => $this->roles[$role],
            'badge_icon' => $this->icons[$role],
            'country' => $request->country,
            'status' => 'active',
            'is_primary' => !TeamPost::where('real_id', $request->real_id)->exists(),

            'permissions' => [
                'add' => $request->has('permission_add'),
                'view' => $request->has('permission_view'),
                'edit' => $request->has('permission_edit'),
                'delete' => $request->has('permission_delete'),
                'suspend' => $request->has('permission_suspend'),
                'withdrawal' => $request->has('permission_withdrawal'),
            ],

            'owner_post_id' => null,
            'assistant_owner_post_id' => null,
            'country_manager_post_id' => null,
            'super_admin_post_id' => null,
            'bd_post_id' => null,
            'agency_post_id' => null,
            'host_post_id' => null,
        ];

        $parent = $request->parent_post_id
            ? TeamPost::where('post_id', $request->parent_post_id)->first()
            : null;

        if ($parent) {
            $data['owner_post_id'] = $parent->owner_post_id;
            $data['assistant_owner_post_id'] = $parent->assistant_owner_post_id;
            $data['country_manager_post_id'] = $parent->country_manager_post_id;
            $data['super_admin_post_id'] = $parent->super_admin_post_id;
            $data['bd_post_id'] = $parent->bd_post_id;
            $data['agency_post_id'] = $parent->agency_post_id;
            $data['host_post_id'] = $parent->host_post_id;
        }

        if ($role === 'assistant_owner') {
            $data['owner_post_id'] = 'OWNER';
            $data['assistant_owner_post_id'] = $postId;
        }

        if ($role === 'country_manager') {
            $data['country_manager_post_id'] = $postId;
        }

        if ($role === 'super_admin') {
            $data['super_admin_post_id'] = $postId;
        }

        if ($role === 'bd') {
            $data['bd_post_id'] = $postId;
        }

        if ($role === 'agency') {
            $data['agency_post_id'] = $postId;
        }

        if ($role === 'host') {
            $data['host_post_id'] = $postId;
        }

        TeamPost::create($data);

        return redirect()
            ->route('admin.team.index', $role)
            ->with('success', 'Post created successfully');
    }

    public function show($id)
    {
        $post = TeamPost::findOrFail($id);

        $allPosts = TeamPost::where('real_id', $post->real_id)
            ->where('status', 'active')
            ->get();

        return view('admin.team.show', compact('post', 'allPosts'));
    }

    public function suspend($id)
    {
        if (session('admin_role') !== 'owner') {
            return back()->with('error', 'Only owner can suspend');
        }

        $post = TeamPost::findOrFail($id);
        $post->status = 'suspended';
        $post->save();

        return back()->with('success', 'Suspended successfully');
    }

    public function countryTeams()
    {
        $countries = TeamPost::whereNotNull('country')
            ->where('country', '!=', '')
            ->get()
            ->groupBy('country')
            ->map(function ($items, $country) {
                return [
                    'country' => $country,
                    'total_posts' => $items->count(),
                    'country_managers' => $items->where('role', 'country_manager')->count(),
                    'super_admins' => $items->where('role', 'super_admin')->count(),
                    'bds' => $items->where('role', 'bd')->count(),
                    'agencies' => $items->where('role', 'agency')->count(),
                    'hosts' => $items->where('role', 'host')->count(),
                ];
            });

        return view('admin.team.country-teams', compact('countries'));
    }

    public function countryView($country)
    {
        $managers = TeamPost::where('country', $country)
            ->where('role', 'country_manager')
            ->where('status', 'active')
            ->get();

        return view('admin.team.country-view', compact('country', 'managers'));
    }

    public function teamTree($post_id)
    {
        $post = TeamPost::where('post_id', $post_id)->firstOrFail();

        $children = TeamPost::where('parent_post_id', $post_id)
            ->where('status', 'active')
            ->get();

        return view('admin.team.team-tree', compact('post', 'children'));
    }

    private function generatePostId($role)
    {
        $prefixes = [
            'assistant_owner' => 'AO',
            'country_manager' => 'CM',
            'super_admin' => 'SA',
            'bd' => 'BD',
            'agency' => 'AG',
            'host' => 'HT',
        ];

        $count = TeamPost::where('role', $role)->count() + 1;

        return $prefixes[$role] . str_pad($count, 6, '0', STR_PAD_LEFT);
    }
}
