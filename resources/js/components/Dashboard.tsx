/**
 * Dashboard Component - Barrel Export Wrapper
 * 
 * This file serves as a backward compatibility wrapper that imports
 * the refactored Dashboard component from the Dashboard folder.
 * 
 * All dashboard-related components have been moved to:
 * ./Dashboard/
 * 
 * For new code, prefer importing directly from the Dashboard folder:
 * import { Dashboard } from '@/components/Dashboard';
 */

// Re-export everything from the Dashboard folder's barrel export
export * from './Dashboard/index';

// Default export for backward compatibility
export { default } from './Dashboard/Dashboard';