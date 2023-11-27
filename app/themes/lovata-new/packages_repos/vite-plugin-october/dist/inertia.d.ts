/**
 * Resolves a page component.
 */
declare function resolvePageComponent(name: string, pages: Record<string, any>, defaultLayout?: any): Promise<any>;

export { resolvePageComponent };
