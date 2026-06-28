import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\NeedController::status
* @see app/Http/Controllers/NeedController.php:46
* @route '/commitments/{commitment}/estado'
*/
export const status = (args: { commitment: number | { id: number } } | [commitment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: status.url(args, options),
    method: 'patch',
})

status.definition = {
    methods: ["patch"],
    url: '/commitments/{commitment}/estado',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\NeedController::status
* @see app/Http/Controllers/NeedController.php:46
* @route '/commitments/{commitment}/estado'
*/
status.url = (args: { commitment: number | { id: number } } | [commitment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { commitment: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { commitment: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            commitment: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        commitment: typeof args.commitment === 'object'
        ? args.commitment.id
        : args.commitment,
    }

    return status.definition.url
            .replace('{commitment}', parsedArgs.commitment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\NeedController::status
* @see app/Http/Controllers/NeedController.php:46
* @route '/commitments/{commitment}/estado'
*/
status.patch = (args: { commitment: number | { id: number } } | [commitment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: status.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\NeedController::status
* @see app/Http/Controllers/NeedController.php:46
* @route '/commitments/{commitment}/estado'
*/
const statusForm = (args: { commitment: number | { id: number } } | [commitment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: status.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::status
* @see app/Http/Controllers/NeedController.php:46
* @route '/commitments/{commitment}/estado'
*/
statusForm.patch = (args: { commitment: number | { id: number } } | [commitment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: status.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

status.form = statusForm

const commitments = {
    status: Object.assign(status, status),
}

export default commitments