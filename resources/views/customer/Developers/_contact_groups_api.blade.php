<div class="text-uppercase text-primary font-medium-2 mb-3">{{ __('locale.developers.contact_groups_api') }}</div>

{!!  __('locale.description.contact_groups_api', ['brandname' => config('app.name')])  !!}

<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup">
                                    {{ route('api.contacts.index') }}
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">{{ __('locale.developers.parameters') }}</div>
<div class="table-responsive">
    <table class="table">
        <thead class="thead-primary">
        <tr>
            <th>{{ __('locale.developers.parameter') }}</th>
            <th>{{ __('locale.labels.required') }}</th>
            <th style="width:50%;">{{ __('locale.labels.description') }}</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>Authorization</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>When calling our API, send your api token with the authentication type set as <code>Bearer</code> (Example: <code>Authorization: Bearer {api_token}</code>)</td>
        </tr>

        <tr>
            <td>Accept</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>Set to <code>application/json</code></td>
        </tr>

        </tbody>
    </table>
</div>


<div class="mt-4 mb-1 font-medium-2 text-primary">Create a group</div>
<p>Creates a new group object. {{ config('app.name') }} returns the created group object with each request.</p>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/contacts
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">{{ __('locale.developers.parameters') }}</div>
<div class="table-responsive">
    <table class="table">
        <thead class="thead-primary">
        <tr>
            <th>{{ __('locale.developers.parameter') }}</th>
            <th>{{ __('locale.labels.required') }}</th>
            <th>{{ __('locale.labels.type') }}</th>
            <th style="width:40%;">{{ __('locale.labels.description') }}</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>name</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>The name of the group</td>
        </tr>


        </tbody>
    </table>
</div>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X POST {{ route('api.contacts.store') }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd' \
     -d "name=codeglen"
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "group data with all details",
}
                                </code>
                            </pre>
<p>If the request failed, an error object will be returned.</p>
<pre>
                                <code class="language-json">
{
    "status": "error",
    "message" : "A human-readable description of the error."
}
                                </code>
                            </pre>


<div class="mt-4 mb-1 font-medium-2 text-primary">View a group</div>
<p>Retrieves the information of an existing group. You only need to supply the unique group ID that was returned upon creation or receiving.</p>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/contacts/<span class="text-danger">{group_id}</span>/show/
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">{{ __('locale.developers.parameters') }}</div>
<div class="table-responsive">
    <table class="table">
        <thead class="thead-primary">
        <tr>
            <th>{{ __('locale.developers.parameter') }}</th>
            <th>{{ __('locale.labels.required') }}</th>
            <th>{{ __('locale.labels.type') }}</th>
            <th style="width:40%;">{{ __('locale.labels.description') }}</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>group_id</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>Contact Groups <code>uid</code></td>
        </tr>

        </tbody>
    </table>
</div>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X POST {{ route('api.contacts.show', ['group_id' => '6065ecdc9184a']) }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd'
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "group data with all details",
}
                                </code>
                            </pre>
<p>If the request failed, an error object will be returned.</p>
<pre>
                                <code class="language-json">
{
    "status": "error",
    "message" : "A human-readable description of the error."
}
                                </code>
                            </pre>


<div class="mt-4 mb-1 font-medium-2 text-primary">Update a group</div>
<p>Updates an existing group. You only need to supply the unique ID that was returned upon creation.</p>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/contacts/<span class="text-danger">{group_id}</span>
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">{{ __('locale.developers.parameters') }}</div>
<div class="table-responsive">
    <table class="table">
        <thead class="thead-primary">
        <tr>
            <th>{{ __('locale.developers.parameter') }}</th>
            <th>{{ __('locale.labels.required') }}</th>
            <th>{{ __('locale.labels.type') }}</th>
            <th style="width:40%;">{{ __('locale.labels.description') }}</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>group_id</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>Contact Groups <code>uid</code></td>
        </tr>

        <tr>
            <td>name</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>New group name</td>
        </tr>

        </tbody>
    </table>
</div>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X PATCH {{ route('api.contacts.update', ['contact' => '6065ecdc9184a']) }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd' \
     -d "name=codeglen new"
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "groups data with all details",
}
                                </code>
                            </pre>
<p>If the request failed, an error object will be returned.</p>
<pre>
                                <code class="language-json">
{
    "status": "error",
    "message" : "A human-readable description of the error."
}
                                </code>
                            </pre>


<div class="mt-4 mb-1 font-medium-2 text-primary">Delete a group</div>
<p>Deletes an existing group. You only need to supply the unique id that was returned upon creation.</p>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/contacts/<span class="text-danger">{group_id}</span>
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">{{ __('locale.developers.parameters') }}</div>
<div class="table-responsive">
    <table class="table">
        <thead class="thead-primary">
        <tr>
            <th>{{ __('locale.developers.parameter') }}</th>
            <th>{{ __('locale.labels.required') }}</th>
            <th>{{ __('locale.labels.type') }}</th>
            <th style="width:40%;">{{ __('locale.labels.description') }}</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>group_id</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>Contact Groups <code>uid</code></td>
        </tr>

        </tbody>
    </table>
</div>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X DELETE {{ route('api.contacts.destroy', ['contact' => '6065ecdc9184a']) }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd'
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "null",
}
                                </code>
                            </pre>
<p>If the request failed, an error object will be returned.</p>
<pre>
                                <code class="language-json">
{
    "status": "error",
    "message" : "A human-readable description of the error."
}
                                </code>
                            </pre>


<div class="mt-4 mb-1 font-medium-2 text-primary">View all groups</div>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/contacts/
                                </code>
                            </pre>


<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X GET {{ route('api.contacts.index') }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd'
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "group data with pagination",
}
                                </code>
                            </pre>
<p>If the request failed, an error object will be returned.</p>
<pre>
                                <code class="language-json">
{
    "status": "error",
    "message" : "A human-readable description of the error."
}
                                </code>
                            </pre>





