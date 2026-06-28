import { queryParams, type RouteQueryOptions, type RouteDefinition, type RouteFormDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\BuildingController::similar
* @see app/Http/Controllers/BuildingController.php:136
* @route '/api/edificios-similares'
*/
export const similar = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: similar.url(options),
    method: 'get',
})

similar.definition = {
    methods: ["get","head"],
    url: '/api/edificios-similares',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BuildingController::similar
* @see app/Http/Controllers/BuildingController.php:136
* @route '/api/edificios-similares'
*/
similar.url = (options?: RouteQueryOptions) => {
    return similar.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BuildingController::similar
* @see app/Http/Controllers/BuildingController.php:136
* @route '/api/edificios-similares'
*/
similar.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: similar.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BuildingController::similar
* @see app/Http/Controllers/BuildingController.php:136
* @route '/api/edificios-similares'
*/
similar.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: similar.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BuildingController::similar
* @see app/Http/Controllers/BuildingController.php:136
* @route '/api/edificios-similares'
*/
const similarForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: similar.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BuildingController::similar
* @see app/Http/Controllers/BuildingController.php:136
* @route '/api/edificios-similares'
*/
similarForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: similar.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BuildingController::similar
* @see app/Http/Controllers/BuildingController.php:136
* @route '/api/edificios-similares'
*/
similarForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: similar.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

similar.form = similarForm

/**
* @see \App\Http\Controllers\BuildingController::create
* @see app/Http/Controllers/BuildingController.php:89
* @route '/edificios/registrar'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/edificios/registrar',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BuildingController::create
* @see app/Http/Controllers/BuildingController.php:89
* @route '/edificios/registrar'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BuildingController::create
* @see app/Http/Controllers/BuildingController.php:89
* @route '/edificios/registrar'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BuildingController::create
* @see app/Http/Controllers/BuildingController.php:89
* @route '/edificios/registrar'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BuildingController::create
* @see app/Http/Controllers/BuildingController.php:89
* @route '/edificios/registrar'
*/
const createForm = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BuildingController::create
* @see app/Http/Controllers/BuildingController.php:89
* @route '/edificios/registrar'
*/
createForm.get = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BuildingController::create
* @see app/Http/Controllers/BuildingController.php:89
* @route '/edificios/registrar'
*/
createForm.head = (options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: create.url({
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

create.form = createForm

/**
* @see \App\Http\Controllers\BuildingController::show
* @see app/Http/Controllers/BuildingController.php:110
* @route '/edificios/{building}'
*/
export const show = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/edificios/{building}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\BuildingController::show
* @see app/Http/Controllers/BuildingController.php:110
* @route '/edificios/{building}'
*/
show.url = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return show.definition.url
            .replace('{building}', parsedArgs.building.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BuildingController::show
* @see app/Http/Controllers/BuildingController.php:110
* @route '/edificios/{building}'
*/
show.get = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BuildingController::show
* @see app/Http/Controllers/BuildingController.php:110
* @route '/edificios/{building}'
*/
show.head = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\BuildingController::show
* @see app/Http/Controllers/BuildingController.php:110
* @route '/edificios/{building}'
*/
const showForm = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BuildingController::show
* @see app/Http/Controllers/BuildingController.php:110
* @route '/edificios/{building}'
*/
showForm.get = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\BuildingController::show
* @see app/Http/Controllers/BuildingController.php:110
* @route '/edificios/{building}'
*/
showForm.head = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'get'> => ({
    action: show.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'HEAD',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'get',
})

show.form = showForm

/**
* @see \App\Http\Controllers\BuildingController::store
* @see app/Http/Controllers/BuildingController.php:101
* @route '/edificios'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/edificios',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\BuildingController::store
* @see app/Http/Controllers/BuildingController.php:101
* @route '/edificios'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\BuildingController::store
* @see app/Http/Controllers/BuildingController.php:101
* @route '/edificios'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BuildingController::store
* @see app/Http/Controllers/BuildingController.php:101
* @route '/edificios'
*/
const storeForm = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BuildingController::store
* @see app/Http/Controllers/BuildingController.php:101
* @route '/edificios'
*/
storeForm.post = (options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: store.url(options),
    method: 'post',
})

store.form = storeForm

/**
* @see \App\Http\Controllers\BuildingController::update
* @see app/Http/Controllers/BuildingController.php:124
* @route '/edificios/{building}'
*/
export const update = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/edificios/{building}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\BuildingController::update
* @see app/Http/Controllers/BuildingController.php:124
* @route '/edificios/{building}'
*/
update.url = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return update.definition.url
            .replace('{building}', parsedArgs.building.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\BuildingController::update
* @see app/Http/Controllers/BuildingController.php:124
* @route '/edificios/{building}'
*/
update.put = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\BuildingController::update
* @see app/Http/Controllers/BuildingController.php:124
* @route '/edificios/{building}'
*/
const updateForm = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

/**
* @see \App\Http\Controllers\BuildingController::update
* @see app/Http/Controllers/BuildingController.php:124
* @route '/edificios/{building}'
*/
updateForm.put = (args: { building: string | { slug: string } } | [building: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteFormDefinition<'post'> => ({
    action: update.url(args, {
        [options?.mergeQuery ? 'mergeQuery' : 'query']: {
            _method: 'PUT',
            ...(options?.query ?? options?.mergeQuery ?? {}),
        }
    }),
    method: 'post',
})

update.form = updateForm

const buildings = {
    similar: Object.assign(similar, similar),
    create: Object.assign(create, create),
    show: Object.assign(show, show),
    store: Object.assign(store, store),
    update: Object.assign(update, update),
}

export default buildings