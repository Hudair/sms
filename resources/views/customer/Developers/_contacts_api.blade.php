<div class="text-uppercase text-primary font-medium-2 mb-3">{{ __('locale.developers.contacts_api') }}</div>

{!!  __('locale.description.contacts_api', ['brandname' => config('app.name')])  !!}

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


<div class="mt-4 mb-1 font-medium-2 text-primary">Create a contact</div>
<p>Creates a new contact object. {{ config('app.name') }} returns the created contact object with each request.</p>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/contacts/<span class="text-danger">{group_id}</span>/store
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
            <td>phone</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>number</td>
            <td>The phone number of the contact.</td>
        </tr>

        <tr>
            <td>first_name</td>
            <td>{{ __('locale.labels.no') }}</td>
            <td>string</td>
            <td>The first name of the contact.</td>
        </tr>

        <tr>
            <td>last_name</td>
            <td>{{ __('locale.labels.no') }}</td>
            <td>string</td>
            <td>The last name of the contact.</td>
        </tr>


        </tbody>
    </table>
</div>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X POST {{ route('api.contact.store', ['group_id' => '6065ecdc9184a']) }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd' \
     -d "phone=31612345678"
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "contacts data with all details",
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


<div class="mt-4 mb-1 font-medium-2 text-primary">View a contact</div>
<p>Retrieves the information of an existing contact. You only need to supply the unique contact uid and group uid that was returned upon creation or receiving.</p>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/contacts/<span class="text-danger">{group_id}</span>/search/<span class="text-danger">{uid}</span>
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
            <td>uid</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>Contact <code>uid</code></td>
        </tr>

        </tbody>
    </table>
</div>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X POST {{ route('api.contact.search', ['group_id' => '6065ecdc9184a', 'uid' => '606732aec8705']) }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd'
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "contacts data with all details",
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


<div class="mt-4 mb-1 font-medium-2 text-primary">Update a contact</div>
<p>Updates an existing contact. You only need to supply the unique uid of contact and contact group uid that was returned upon creation.</p>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/contacts/<span class="text-danger">{group_id}</span>/update/<span class="text-danger">{uid}</span>
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
            <td>uid</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>Contact <code>uid</code></td>
        </tr>

        <tr>
            <td>phone</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>number</td>
            <td>The phone number of the contact.</td>
        </tr>

        <tr>
            <td>first_name</td>
            <td>{{ __('locale.labels.no') }}</td>
            <td>string</td>
            <td>The first name of the contact.</td>
        </tr>

        <tr>
            <td>last_name</td>
            <td>{{ __('locale.labels.no') }}</td>
            <td>string</td>
            <td>The last name of the contact.</td>
        </tr>


        </tbody>
    </table>
</div>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X PATCH {{ route('api.contact.update', ['group_id' => '6065ecdc9184a', 'uid' => '606732aec8705']) }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd' \
     -d "phone=31612345678"
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "contacts data with all details",
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


<div class="mt-4 mb-1 font-medium-2 text-primary">Delete a contact</div>
<p>Deletes an existing contact. You only need to supply the unique contact uid and group uid that was returned upon creation.</p>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/contacts/<span class="text-danger">{group_id}</span>/delete/<span class="text-danger">{uid}</span>
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
            <td>uid</td>
            <td>
                <div class="badge badge-primary text-uppercase mr-1 mb-1"><span>{{ __('locale.labels.yes') }}</span></div>
            </td>
            <td>string</td>
            <td>Contact <code>uid</code></td>
        </tr>

        </tbody>
    </table>
</div>

<div class="mt-2 font-medium-2 text-primary"> Example request</div>
<pre>
                                <code class="language-php">
curl -X DELETE {{ route('api.contact.delete', ['group_id' => '6065ecdc9184a', 'uid' => '606732aec8705']) }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd'
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "contacts data with all details",
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


<div class="mt-4 mb-1 font-medium-2 text-primary">View all contacts in group</div>
<p class="font-medium-2 mt-2">{{ __('locale.developers.api_endpoint') }}</p>

<pre>
                                <code class="language-markup text-primary">
                                    {{config('app.url')}}/api/v3/contacts/<span class="text-danger">{group_id}</span>/all
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
curl -X POST {{ route('api.contact.all', ['group_id' => '6065ecdc9184a']) }} \
     -H 'Authorization: Bearer 7|xs6pv2dspHJq8sWLhrpNFH5YLilMRQcVxLwSw2Sd'
                                </code>
                            </pre>

<div class="mt-2 font-medium-2 text-primary">Returns</div>
<p>Returns a contact object if the request was successful. </p>
<pre>
                                <code class="language-json">
{
    "status": "success",
    "data": "contacts data with pagination",
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





