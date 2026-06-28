import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\NeedController::store
* @see app/Http/Controllers/NeedController.php:21
* @route '/edificios/{building}/necesidades'
*/
export const store = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/edificios/{building}/necesidades',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NeedController::store
* @see app/Http/Controllers/NeedController.php:21
* @route '/edificios/{building}/necesidades'
*/
store.url = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { building: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { building: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            building: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        building: typeof args.building === 'object'
        ? args.building.slug
        : args.building,
    }

    return store.definition.url
            .replace('{building}', parsedArgs.building.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\NeedController::store
* @see app/Http/Controllers/NeedController.php:21
* @route '/edificios/{building}/necesidades'
*/
store.post = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::store
* @see app/Http/Controllers/NeedController.php:21
* @route '/edificios/{building}/necesidades'
*/
const storeForm = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::store
* @see app/Http/Controllers/NeedController.php:21
* @route '/edificios/{building}/necesidades'
*/
storeForm.post = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(args, options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\NeedController::batch
* @see app/Http/Controllers/NeedController.php:28
* @route '/edificios/{building}/necesidades/lote'
*/
export const batch = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: batch.url(args, options),
    method: 'post',
})

batch.definition = {
    methods: ["post"],
    url: '/edificios/{building}/necesidades/lote',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NeedController::batch
* @see app/Http/Controllers/NeedController.php:28
* @route '/edificios/{building}/necesidades/lote'
*/
batch.url = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { building: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { building: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            building: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        building: typeof args.building === 'object'
        ? args.building.slug
        : args.building,
    }

    return batch.definition.url
            .replace('{building}', parsedArgs.building.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\NeedController::batch
* @see app/Http/Controllers/NeedController.php:28
* @route '/edificios/{building}/necesidades/lote'
*/
batch.post = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: batch.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::batch
* @see app/Http/Controllers/NeedController.php:28
* @route '/edificios/{building}/necesidades/lote'
*/
const batchForm = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: batch.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::batch
* @see app/Http/Controllers/NeedController.php:28
* @route '/edificios/{building}/necesidades/lote'
*/
batchForm.post = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: batch.url(args, options),
    method: 'post',
})

batch.form = batchForm

/**
* @see \App\Http\Controllers\NeedController::commit
* @see app/Http/Controllers/NeedController.php:37
* @route '/necesidades/{need}/comprometerse'
*/
export const commit = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: commit.url(args, options),
    method: 'post',
})

commit.definition = {
    methods: ["post"],
    url: '/necesidades/{need}/comprometerse',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NeedController::commit
* @see app/Http/Controllers/NeedController.php:37
* @route '/necesidades/{need}/comprometerse'
*/
commit.url = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { need: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { need: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            need: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        need: typeof args.need === 'object'
        ? args.need.id
        : args.need,
    }

    return commit.definition.url
            .replace('{need}', parsedArgs.need.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\NeedController::commit
* @see app/Http/Controllers/NeedController.php:37
* @route '/necesidades/{need}/comprometerse'
*/
commit.post = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: commit.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::commit
* @see app/Http/Controllers/NeedController.php:37
* @route '/necesidades/{need}/comprometerse'
*/
const commitForm = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: commit.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::commit
* @see app/Http/Controllers/NeedController.php:37
* @route '/necesidades/{need}/comprometerse'
*/
commitForm.post = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: commit.url(args, options),
    method: 'post',
})

commit.form = commitForm

const needs = {
    store: Object.assign(store, store),
    batch: Object.assign(batch, batch),
    commit: Object.assign(commit, commit),
}

export default needs