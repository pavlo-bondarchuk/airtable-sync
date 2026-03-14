<?php
if (!defined('ABSPATH')) exit;

class AT_Sync_Airtable_Client
{
    private $api_key;
    private $base_id;
    private $table;

    public function __construct()
    {
        $this->api_key = get_option('airtable_api_key', '');
        $this->base_id = get_option('airtable_base_id', '');
        $this->table   = get_option('airtable_table', '');
    }

    private function headers()
    {
        return [
            'Authorization' => 'Bearer ' . $this->api_key,
            'Content-Type'  => 'application/json'
        ];
    }

    private function endpoint($path = '')
    {
        return "https://api.airtable.com/v0/{$this->base_id}/{$this->table}{$path}";
    }

    /**
     * Fetch all rows (paginated), safe version
     */
    public function fetch_rows()
    {
        if (!$this->api_key || !$this->base_id || !$this->table) {
            return new WP_Error('missing_config', 'Airtable API configuration is incomplete.');
        }

        $records   = [];
        $offset    = null;
        $max_pages = 20;              // Hard stop to avoid infinite loop
        $counter   = 0;

        atsync_log("Airtable Sync: fetch_rows() started");

        do {

            $counter++;
            if ($counter > $max_pages) {
                atsync_log("Airtable Sync: pagination aborted (limit reached)");
                return new WP_Error('too_many_pages', 'Airtable pagination exceeded safe limit.');
            }

            $url = $this->endpoint($offset ? "?offset={$offset}" : '');
            atsync_log("Airtable Sync: Request page {$counter}, URL={$url}");

            $response = wp_remote_get($url, [
                'headers' => $this->headers(),
                'timeout' => 60,        // Increased timeout
            ]);

            if (is_wp_error($response)) {
                atsync_log("Airtable Sync: HTTP error — " . $response->get_error_message());
                return $response;
            }

            $body = json_decode(wp_remote_retrieve_body($response), true);

            if (!is_array($body) || !isset($body['records'])) {
                atsync_log("Airtable Sync: invalid or empty response body");
                return new WP_Error('invalid_response', 'Unexpected Airtable API response.');
            }

            // Merge records
            if (!empty($body['records'])) {
                $records = array_merge($records, $body['records']);
            }

            // Pagination
            $offset = $body['offset'] ?? null;
            atsync_log("Airtable Sync: Next offset=" . ($offset ?: 'none'));

            // Handle API rate limit
            usleep(100000); // 0.2 sec

        } while ($offset);

        atsync_log("Airtable Sync: fetch_rows() finished, total=" . count($records));

        return $records;
    }


    /**
     * Fetch field names (columns)
     */
    public function detect_columns()
    {
        $rows = $this->fetch_rows();

        if (is_wp_error($rows)) {
            return $rows;
        }

        if (empty($rows)) {
            return [];
        }

        $fields = [];

        foreach ($rows as $row) {
            if (!empty($row['fields']) && is_array($row['fields'])) {
                foreach ($row['fields'] as $field => $value) {
                    $fields[$field] = true;
                }
            }
        }

        return array_keys($fields);
    }

}