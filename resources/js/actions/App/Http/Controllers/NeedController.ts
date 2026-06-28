import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../../../wayfinder'
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
* @see \App\Http\Controllers\NeedController::storeBatch
* @see app/Http/Controllers/NeedController.php:28
* @route '/edificios/{building}/necesidades/lote'
*/
export const storeBatch = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeBatch.url(args, options),
    method: 'post',
})

storeBatch.definition = {
    methods: ["post"],
    url: '/edificios/{building}/necesidades/lote',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NeedController::storeBatch
* @see app/Http/Controllers/NeedController.php:28
* @route '/edificios/{building}/necesidades/lote'
*/
storeBatch.url = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return storeBatch.definition.url
            .replace('{building}', parsedArgs.building.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\NeedController::storeBatch
* @see app/Http/Controllers/NeedController.php:28
* @route '/edificios/{building}/necesidades/lote'
*/
storeBatch.post = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: storeBatch.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::storeBatch
* @see app/Http/Controllers/NeedController.php:28
* @route '/edificios/{building}/necesidades/lote'
*/
const storeBatchForm = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeBatch.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::storeBatch
* @see app/Http/Controllers/NeedController.php:28
* @route '/edificios/{building}/necesidades/lote'
*/
storeBatchForm.post = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: storeBatch.url(args, options),
    method: 'post',
})

storeBatch.form = storeBatchForm

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

/**
* @see \App\Http\Controllers\NeedController::cancel
* @see app/Http/Controllers/NeedController.php:53
* @route '/necesidades/{need}/cancelar'
*/
export const cancel = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

cancel.definition = {
    methods: ["post"],
    url: '/necesidades/{need}/cancelar',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NeedController::cancel
* @see app/Http/Controllers/NeedController.php:53
* @route '/necesidades/{need}/cancelar'
*/
cancel.url = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return cancel.definition.url
            .replace('{need}', parsedArgs.need.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\NeedController::cancel
* @see app/Http/Controllers/NeedController.php:53
* @route '/necesidades/{need}/cancelar'
*/
cancel.post = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: cancel.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::cancel
* @see app/Http/Controllers/NeedController.php:53
* @route '/necesidades/{need}/cancelar'
*/
const cancelForm = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::cancel
* @see app/Http/Controllers/NeedController.php:53
* @route '/necesidades/{need}/cancelar'
*/
cancelForm.post = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: cancel.url(args, options),
    method: 'post',
})

cancel.form = cancelForm

/**
* @see \App\Http\Controllers\NeedController::reopen
* @see app/Http/Controllers/NeedController.php:60
* @route '/necesidades/{need}/reabrir'
*/
export const reopen = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reopen.url(args, options),
    method: 'post',
})

reopen.definition = {
    methods: ["post"],
    url: '/necesidades/{need}/reabrir',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\NeedController::reopen
* @see app/Http/Controllers/NeedController.php:60
* @route '/necesidades/{need}/reabrir'
*/
reopen.url = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return reopen.definition.url
            .replace('{need}', parsedArgs.need.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\NeedController::reopen
* @see app/Http/Controllers/NeedController.php:60
* @route '/necesidades/{need}/reabrir'
*/
reopen.post = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: reopen.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::reopen
* @see app/Http/Controllers/NeedController.php:60
* @route '/necesidades/{need}/reabrir'
*/
const reopenForm = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reopen.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::reopen
* @see app/Http/Controllers/NeedController.php:60
* @route '/necesidades/{need}/reabrir'
*/
reopenForm.post = (args: { need: number | { id: number } } | [need: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: reopen.url(args, options),
    method: 'post',
})

reopen.form = reopenForm

/**
* @see \App\Http\Controllers\NeedController::updateCommitmentStatus
* @see app/Http/Controllers/NeedController.php:46
* @route '/commitments/{commitment}/estado'
*/
export const updateCommitmentStatus = (args: { commitment: number | { id: number } } | [commitment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateCommitmentStatus.url(args, options),
    method: 'patch',
})

updateCommitmentStatus.definition = {
    methods: ["patch"],
    url: '/commitments/{commitment}/estado',
} satisfies RouteDefinition<["patch"]>

/**
* @see \App\Http\Controllers\NeedController::updateCommitmentStatus
* @see app/Http/Controllers/NeedController.php:46
* @route '/commitments/{commitment}/estado'
*/
updateCommitmentStatus.url = (args: { commitment: number | { id: number } } | [commitment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
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

    return updateCommitmentStatus.definition.url
            .replace('{commitment}', parsedArgs.commitment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\NeedController::updateCommitmentStatus
* @see app/Http/Controllers/NeedController.php:46
* @route '/commitments/{commitment}/estado'
*/
updateCommitmentStatus.patch = (args: { commitment: number | { id: number } } | [commitment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: updateCommitmentStatus.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\NeedController::updateCommitmentStatus
* @see app/Http/Controllers/NeedController.php:46
* @route '/commitments/{commitment}/estado'
*/
const updateCommitmentStatusForm = (args: { commitment: number | { id: number } } | [commitment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateCommitmentStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\NeedController::updateCommitmentStatus
* @see app/Http/Controllers/NeedController.php:46
* @route '/commitments/{commitment}/estado'
*/
updateCommitmentStatusForm.patch = (args: { commitment: number | { id: number } } | [commitment: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: updateCommitmentStatus.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PATCH',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

updateCommitmentStatus.form = updateCommitmentStatusForm

const NeedController = { store, storeBatch, commit, cancel, reopen, updateCommitmentStatus }

export default NeedController