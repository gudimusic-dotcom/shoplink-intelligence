# SHOPLINK Intelligence Project Bootstrap

This file is the redacted ITHZ-MCP bootstrap for the current project.

## Project

- Name: SHOPLINK Intelligence
- Local root: `/Users/peterzatyko/Projects/Shoplink Intelligence`
- Laravel app root: `platform/`
- Stage URL: `https://shoplinkstage.1web.sk`

## Continuation Docs

Read these first when resuming work:

1. `docs/CURRENT_HANDOFF.md`
2. `docs/WORKSTATE.md`
3. `docs/SESSION_LOG.md`
4. `docs/RESUME_PROTOCOL.md`

## Current UI Direction

- Clean, premium, dark SaaS interface.
- Avoid browser-default and light TallStack UI appearances where Shoplink styling already exists.
- Current visual focus: TallStack date picker in task create/edit views.

## ITHZ-MCP State

- macOS native `ithz-native` is not available in the official package used here.
- MCP fallback is installed with legacy storage.
- `project.ithz` native archive storage is deferred until a compatible macOS `ithz-native` is available.

## Secret Handling

Do not store raw `.env` values, credentials, private keys, tokens, passwords, or full secret-bearing files in project memory.
