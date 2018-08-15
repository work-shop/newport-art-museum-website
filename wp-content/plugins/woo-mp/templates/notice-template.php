<script type="text/template" id="notice-template">
    <div class="notice notice-<%= type %>">
        <div class="notice-main">
            <div class="notice-content">
                <p><%= message %></p>
                <% if (details) { %>
                    <div id="notice-details-<%= id %>" class="notice-details" hidden><%= details %></div>
                <% } %>
            </div>
            <% if (details) { %>
                <div>
                    <p>
                        <button type="button" class="button button-link" data-toggle="collapse" aria-controls="notice-details-<%= id %>" aria-expanded="false">Details</button>
                    </p>
                </div>
            <% } %>
        </div>
    </div>
</script>