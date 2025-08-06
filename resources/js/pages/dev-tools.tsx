import { Head, Link } from '@inertiajs/react';
import { Bug, Database, Users, Settings } from 'lucide-react';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import AppLayout from '@/layouts/app-layout';

export default function DevTools() {
    return (
        <AppLayout>
            <Head title="Development Tools" />
            
            <div className="space-y-6">
                <div>
                    <h2 className="text-3xl font-bold tracking-tight">Development Tools</h2>
                    <p className="text-muted-foreground">
                        Debugging and monitoring tools for development
                    </p>
                </div>

                <div className="grid gap-6 md:grid-cols-2">
                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Database className="h-5 w-5" />
                                Laravel Telescope
                            </CardTitle>
                            <CardDescription>
                                Monitor requests, queries, jobs, exceptions, and more
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <p className="text-sm text-muted-foreground">
                                    Telescope provides insight into the requests coming into your application, 
                                    exceptions, log entries, database queries, queued jobs, mail, notifications, 
                                    cache operations, scheduled tasks, variable dumps and more.
                                </p>
                                <Button asChild>
                                    <a href="/telescope" target="_blank">
                                        Open Telescope
                                    </a>
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Bug className="h-5 w-5" />
                                Laravel Debugbar
                            </CardTitle>
                            <CardDescription>
                                In-page debugging toolbar with performance metrics
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <p className="text-sm text-muted-foreground">
                                    The Debugbar appears at the bottom of each page showing queries, 
                                    request data, memory usage, execution time, and more. It's automatically 
                                    enabled in development mode.
                                </p>
                                <Button variant="outline" disabled>
                                    Always Visible on Pages
                                </Button>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Users className="h-5 w-5" />
                                Multi-Tenancy Debug
                            </CardTitle>
                            <CardDescription>
                                Tools for debugging tenant-specific issues
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-4">
                                <p className="text-sm text-muted-foreground">
                                    Switch between tenants, inspect tenant databases, and monitor 
                                    tenant-specific operations. Telescope is configured to focus on 
                                    central application requests.
                                </p>
                                <Link href={route('workspaces.index')}>
                                    <Button variant="outline">
                                        Manage Workspaces
                                    </Button>
                                </Link>
                            </div>
                        </CardContent>
                    </Card>

                    <Card>
                        <CardHeader>
                            <CardTitle className="flex items-center gap-2">
                                <Settings className="h-5 w-5" />
                                Configuration
                            </CardTitle>
                            <CardDescription>
                                Environment and debugging configuration
                            </CardDescription>
                        </CardHeader>
                        <CardContent>
                            <div className="space-y-2 text-sm">
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Environment:</span>
                                    <code className="bg-muted px-1 rounded">local</code>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Debug Mode:</span>
                                    <code className="bg-muted px-1 rounded">enabled</code>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Debugbar:</span>
                                    <code className="bg-muted px-1 rounded">enabled</code>
                                </div>
                                <div className="flex justify-between">
                                    <span className="text-muted-foreground">Telescope:</span>
                                    <code className="bg-muted px-1 rounded">enabled</code>
                                </div>
                            </div>
                        </CardContent>
                    </Card>
                </div>

                <div className="rounded-lg bg-amber-50 p-4 dark:bg-amber-950">
                    <div className="flex">
                        <div className="ml-3">
                            <h3 className="text-sm font-medium text-amber-800 dark:text-amber-200">
                                Development Only
                            </h3>
                            <div className="mt-2 text-sm text-amber-700 dark:text-amber-300">
                                <p>
                                    These tools are only available in the local development environment. 
                                    They will be automatically disabled in production for security and performance.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </AppLayout>
    );
}