Admin Notification: Banner Campaign Finished
{if $admin_review_needed}({_MD_BANNERSTATS_EMAIL_ADMIN_REVIEW_NEEDED}){/if}

Banner Name: {BANNER_NAME} (ID: {BANNER_ID})
Client Name: {CLIENT_NAME}
Client Email: {CLIENT_EMAIL}

Impressions Served: {impressions_served}
Clicks Received: {clicks_received}
Reason for Conclusion: {finish_reason}
Date Concluded: {date_finished}

{if $admin_review_needed}
IMPORTANT: Admin review is needed. This might be due to an issue during the archival process (e.g., banner copied but not deleted from active list).
{/if}

Manage Banner/View Details:
{admin_link}

Client Statistics:
{CLIENT_STATS_URL}

Site: {X_SITENAME} ({X_SITEURL})
