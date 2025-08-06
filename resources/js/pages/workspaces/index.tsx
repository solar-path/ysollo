import { Head, Link, router } from '@inertiajs/react';
import { Plus, Users, DollarSign, Calendar, ExternalLink } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';
import { Badge } from '@/components/ui/badge';

interface Workspace {
    id: string;
    workspace_name: string;
    slug: string;
    domain: string;
    role: 'owner' | 'admin' | 'member';
    status: 'active' | 'pending' | 'suspended';
    joined_at: string;
    owner: {
        id: number;
        name: string;
        email: string;
    };
    users_count: number;
    monthly_cost: number;
    created_at: string;
}

interface OwnedWorkspace {
    id: string;
    workspace_name: string;
    slug: string;
    domain: string;
    users_count: number;
    monthly_cost: number;
    created_at: string;
}

interface Props {
    workspaces: Workspace[];
    ownedWorkspaces: OwnedWorkspace[];
    totalMonthlyBilling: number;
}

export default function WorkspacesIndex({ workspaces, ownedWorkspaces, totalMonthlyBilling }: Props) {
    const handleSwitchWorkspace = (tenantId: string) => {
        router.post(route('workspaces.switch', tenantId));
    };

    return (
        <AppLayout>
            <Head title="Workspaces" />
            
            <div className="space-y-6">
                <div className="flex items-center justify-between">
                    <div>
                        <h2 className="text-3xl font-bold tracking-tight">Workspaces</h2>
                        <p className="text-muted-foreground">
                            Manage your workspaces and billing
                        </p>
                    </div>
                    <Link href={route('register')}>
                        <Button>
                            <Plus className="mr-2 h-4 w-4" />
                            Create Workspace
                        </Button>
                    </Link>
                </div>

                {ownedWorkspaces.length > 0 && (
                    <div className="grid gap-4 md:grid-cols-3">
                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">
                                    Total Workspaces
                                </CardTitle>
                                <Users className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">{ownedWorkspaces.length}</div>
                                <p className="text-xs text-muted-foreground">
                                    Workspaces you own
                                </p>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">
                                    Total Users
                                </CardTitle>
                                <Users className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">
                                    {ownedWorkspaces.reduce((sum, w) => sum + w.users_count, 0)}
                                </div>
                                <p className="text-xs text-muted-foreground">
                                    Across all owned workspaces
                                </p>
                            </CardContent>
                        </Card>
                        <Card>
                            <CardHeader className="flex flex-row items-center justify-between space-y-0 pb-2">
                                <CardTitle className="text-sm font-medium">
                                    Monthly Billing
                                </CardTitle>
                                <DollarSign className="h-4 w-4 text-muted-foreground" />
                            </CardHeader>
                            <CardContent>
                                <div className="text-2xl font-bold">${totalMonthlyBilling}</div>
                                <p className="text-xs text-muted-foreground">
                                    $25 per user per month
                                </p>
                            </CardContent>
                        </Card>
                    </div>
                )}

                <div className="space-y-4">
                    <h3 className="text-lg font-semibold">Your Workspaces</h3>
                    {workspaces.length === 0 ? (
                        <Card>
                            <CardContent className="flex flex-col items-center justify-center py-8">
                                <Users className="h-12 w-12 text-muted-foreground mb-4" />
                                <p className="text-lg font-medium">No workspaces yet</p>
                                <p className="text-sm text-muted-foreground">
                                    Create your first workspace to get started
                                </p>
                            </CardContent>
                        </Card>
                    ) : (
                        <div className="grid gap-4 md:grid-cols-2 lg:grid-cols-3">
                            {workspaces.map((workspace) => (
                                <Card key={workspace.id} className="hover:shadow-lg transition-shadow">
                                    <CardHeader>
                                        <div className="flex items-start justify-between">
                                            <div>
                                                <CardTitle className="text-lg">
                                                    {workspace.workspace_name}
                                                </CardTitle>
                                                <CardDescription className="text-xs mt-1">
                                                    {workspace.domain}
                                                </CardDescription>
                                            </div>
                                            <Badge variant={workspace.role === 'owner' ? 'default' : 'secondary'}>
                                                {workspace.role}
                                            </Badge>
                                        </div>
                                    </CardHeader>
                                    <CardContent className="space-y-4">
                                        <div className="space-y-2 text-sm">
                                            <div className="flex items-center justify-between">
                                                <span className="text-muted-foreground">Owner</span>
                                                <span className="font-medium">{workspace.owner.name}</span>
                                            </div>
                                            <div className="flex items-center justify-between">
                                                <span className="text-muted-foreground">Users</span>
                                                <span className="font-medium">{workspace.users_count}</span>
                                            </div>
                                            {workspace.role === 'owner' && (
                                                <div className="flex items-center justify-between">
                                                    <span className="text-muted-foreground">Monthly</span>
                                                    <span className="font-medium">${workspace.monthly_cost}</span>
                                                </div>
                                            )}
                                            <div className="flex items-center justify-between">
                                                <span className="text-muted-foreground">Joined</span>
                                                <span className="font-medium">
                                                    {new Date(workspace.joined_at).toLocaleDateString()}
                                                </span>
                                            </div>
                                        </div>
                                        <div className="flex gap-2">
                                            <Button 
                                                variant="outline" 
                                                size="sm" 
                                                className="flex-1"
                                                onClick={() => handleSwitchWorkspace(workspace.id)}
                                            >
                                                <ExternalLink className="mr-2 h-3 w-3" />
                                                Open
                                            </Button>
                                            {workspace.role === 'owner' && (
                                                <Link 
                                                    href={route('workspaces.show', workspace.id)}
                                                    className="flex-1"
                                                >
                                                    <Button variant="outline" size="sm" className="w-full">
                                                        Manage
                                                    </Button>
                                                </Link>
                                            )}
                                        </div>
                                    </CardContent>
                                </Card>
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AppLayout>
    );
}