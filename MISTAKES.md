# MISTAKES

Fixes that were tried and did not work.

## Contact form mail lands in junk — solved by a Gmail filter, not by changing the mail

Setup: `mail_handler.php` sends through Gmail SMTP as a separate Gmail account (app password) to
`anthonybo.dev@gmail.com`, visitor in Reply-To.

### 2026-11-10: changed the sender display name

- **Changed:** From name `anthonybo.com contact form` -> `Anthony Bocchino website`; also restyled the HTML body.
- **Why it looked right:** a domain-like display name on a gmail.com address can read as impersonation.
- **What happened:** the next message still went to junk.
- **What was measured:** only "user reports it is in junk". The received message's headers and Gmail's
  "why is this in spam" banner were never read, so the cause is unknown. Which filter is acting (Gmail's
  spam filter or a mail app's own junk filter) is also unknown.
- **Confound:** both test messages were poor spam tests. The first said "test" with Reply-To equal to the
  recipient; the second had a `<script>alert(1)</script>` string in the body and an `example.com` Reply-To.

### Resolution (2026-11-10)

Measured in Gmail on the web for the receiving account: SPF PASS, DKIM PASS (gmail.com), DMARC PASS.
Gmail's stated reason was only "similar to messages that were identified as spam in the past", so it is
a content/reputation judgement, not an authentication failure. Nothing in `mail_handler.php` fixes that.

Fix: a filter in the receiving account, `from:(the sending account) subject:(Message from anthonybo.com)`
-> "Never send it to Spam". The next form submission landed in Inbox / Primary.

If the subject line or sending account in `mail_handler.php` changes, the filter must change with it.
