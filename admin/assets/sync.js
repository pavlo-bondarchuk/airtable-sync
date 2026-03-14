jQuery(function ($) {

    // ---------------------------------------------------
    // 1) FIELD MAPPING — LOAD COLUMNS + WP FIELDS
    // ---------------------------------------------------
    $("#at-load-columns").on("click", function () {

        const root = $("#airtable-mapping-root");
        const existingMapping = root.data("mapping") || {};

        $("#at-mapping-loading").show();

        //
        // STEP 1: Load Airtable columns
        //
        $.post(
            ATSync.ajaxUrl,
            {
                action: "airtable_get_columns",
                _ajax_nonce: ATSync.nonce
            },
            function (res) {

                $("#at-mapping-loading").hide();

                if (!res || !res.success) {
                    console.error(res);
                    alert("Error loading Airtable columns");
                    return;
                }

                const columns = res.data.columns || [];

                //
                // STEP 2: Load WP fields
                //
                $.post(
                    ATSync.ajaxUrl,
                    {
                        action: "airtable_get_wp_fields",
                        _ajax_nonce: ATSync.nonce
                    },
                    function (wpRes) {

                        if (!wpRes || !wpRes.success) {
                            console.error(wpRes);
                            alert("Error loading WordPress fields");
                            return;
                        }

                        const wpFields = wpRes.data;

                        $("#at-mapping-form").show();
                        const tbody = $("#at-mapping-table-body");
                        tbody.empty();

                        //
                        // Render mapping table rows
                        //
                        columns.forEach(function (col) {

                            let options = '<option value="">— Select —</option>';

                            wpFields.forEach(function (f) {
                                options += `<option value="${f.key}">${f.label}</option>`;
                            });

                            tbody.append(`
                                <tr>
                                    <td>${col}</td>
                                    <td>
                                        <select name="mapping[${col}]">
                                            ${options}
                                        </select>
                                    </td>
                                </tr>
                            `);
                        });

                        //
                        // Apply saved mapping
                        //
                        Object.entries(existingMapping).forEach(([col, field]) => {
                            const select = $(`select[name="mapping[${col}]"]`);
                            if (select.length) select.val(field);
                        });
                    }
                );
            }
        );
    });


    // ---------------------------------------------------
    // 2) FIELD MAPPING — SAVE MAPPING
    // ---------------------------------------------------
    $("#at-mapping-form").on("submit", function (e) {
        e.preventDefault();

        const raw = $(this).serializeArray();
        const mapping = {};

        raw.forEach(item => {
            const key = item.name.replace(/^mapping\[|\]$/g, "");
            mapping[key] = item.value;
        });

        $.post(
            ATSync.ajaxUrl,
            {
                action: "airtable_save_mapping",
                _ajax_nonce: ATSync.nonce,
                mapping: mapping
            },
            function (res) {

                if (!res || !res.success) {
                    console.error(res);
                    alert("Failed to save mapping");
                    return;
                }

                $("#at-mapping-message").html(
                    "<div class='updated notice'><p>Mapping saved.</p></div>"
                );
            }
        );
    });


    // ---------------------------------------------------
    // 3) IMPORT / SYNC — RUN SYNC
    // ---------------------------------------------------
    $("#at-sync-run").on("click", function () {

        $("#at-sync-result").hide();
        $("#at-sync-loading").show();

        // 1) Спочатку отримуємо всі Airtable записи
        $.post(
            ATSync.ajaxUrl,
            {
                action: "airtable_pull",
                nonce: ATSync.nonce
            },
            function (res) {

                if (!res.records || !Array.isArray(res.records)) {
                    $("#at-sync-loading").hide();
                    alert("Failed to load Airtable records");
                    return;
                }

                const records = res.records;
                const total = records.length;
                let index = 0;

                $("#at-sync-result")
                    .show()
                    .html(`<p>Starting import…</p>`);

                // 2) Queue function
                function processNext() {

                    if (index >= total) {
                        $("#at-sync-loading").hide();
                        $("#at-sync-result").html(`<p>Done! Imported ${total} records.</p>`);
                        return;
                    }

                    const r = records[index];

                    $("#at-sync-result").html(
                        `<p>Importing ${index + 1} / ${total}…</p>`
                    );

                    $.post(
                        ATSync.ajaxUrl,
                        {
                            action: "airtable_sync_import_single",
                            nonce: ATSync.nonce,
                            record: r
                        },
                        function (response) {
                            index++;
                            setTimeout(processNext, 150); // маленький крок для throttle
                        }
                    ).fail(function () {
                        index++;
                        setTimeout(processNext, 250);
                    });
                }

                // 3) старт черги
                processNext();
            }
        );

    });

});