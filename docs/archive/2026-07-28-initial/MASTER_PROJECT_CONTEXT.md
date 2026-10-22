# SHOPLINK Intelligence

# MASTER PROJECT CONTEXT

Version: 1.0

Status: Living Document

Document Owner: Project Architect

Classification: Internal

---

# Purpose

This document is the authoritative source of truth for the SHOPLINK Intelligence project.

Every AI assistant, software architect, developer, product owner, or stakeholder participating in this project must read and understand this document before making any architectural, business, or implementation decisions.

This document defines the vision, philosophy, business goals, architectural principles, constraints, terminology, and long-term direction of the project.

Whenever a conflict exists between implementation and this document, this document takes precedence unless explicitly approved by the project owner.

SHOPLINK Intelligence is considered a living project. Therefore this document is expected to evolve together with the platform while preserving architectural consistency.

---

# Instructions for AI Assistants

This section defines mandatory behaviour for every AI system working on this project.

The AI assistant must:

• Treat this document as the single source of truth.

• Never silently change business rules.

• Never redesign existing architecture without approval.

• Preserve naming consistency.

• Preserve database consistency.

• Preserve modular architecture.

• Never introduce unnecessary complexity.

• Prefer simple solutions over clever solutions.

• Generate documentation before implementation.

• Explain architectural trade-offs when proposing alternatives.

• Clearly distinguish between assumptions and documented facts.

• Keep all future documentation compatible with this document.

If required information is missing, propose alternatives instead of inventing business rules.

---

# Project Identity

Project Name

SHOPLINK Intelligence

Project Type

Internal Business Intelligence and Operations Platform

Current Status

Greenfield Project

Primary Business

E-commerce

Primary Company

MobileGo

Primary Users

Internal employees

Future Vision

Commercial SaaS platform (possible)

---

# Executive Summary

SHOPLINK Intelligence is not a dashboard.

It is not a reporting tool.

It is not an ERP replacement.

It is not another CRM.

SHOPLINK Intelligence is designed to become the operational brain of the company.

Its purpose is to centralize information, automate operational workflows, support business decisions, and reduce manual work across all departments.

Every employee should start the working day inside SHOPLINK Intelligence.

The platform connects data, processes, people, tasks and artificial intelligence into one integrated operating environment.

The long-term vision is to transform fragmented operational knowledge into a single intelligent business platform.

---

# Why This Project Exists

The company currently operates using multiple disconnected systems.

Business information is scattered across:

• Mobdata exports

• Excel spreadsheets

• Emails

• Marketplace portals

• Internal knowledge

• Manual reporting

• Individual employee experience

As a result:

• information is duplicated

• reporting is slow

• operational decisions depend on individuals

• trends are difficult to detect

• tasks are often reactive instead of proactive

• valuable business knowledge is lost

SHOPLINK Intelligence solves these problems by becoming the central operational platform.

---

# Vision

The long-term vision is to build an intelligent business operating system capable of supporting every important operational decision made inside the company.

Instead of employees searching for information, the system should proactively deliver the right information to the right people at the right time.

Artificial intelligence should assist employees in understanding what is happening, why it is happening, and what should be investigated next.

The platform should continuously evolve into the company's operational memory.

---

# Mission

The mission of SHOPLINK Intelligence is to:

• centralize operational knowledge

• improve business visibility

• automate repetitive work

• increase decision quality

• reduce operational risks

• improve employee productivity

• make company knowledge searchable

• support sustainable growth

Every feature implemented in the platform must support at least one of these objectives.

---

# Core Philosophy

The project follows one simple philosophy:

"Business first.
Technology second."

Technology exists only to solve business problems.

Every implemented feature must provide measurable business value.

No functionality should exist merely because it is technically interesting.

Complexity is considered technical debt unless it delivers proportional business value.

The platform must remain understandable, maintainable and extensible over many years.

Architecture should enable change rather than resist it.