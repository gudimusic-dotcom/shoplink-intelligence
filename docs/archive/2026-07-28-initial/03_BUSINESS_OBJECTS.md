# SHOPLINK Intelligence

Document: BUSINESS_OBJECTS

Version: 1.0

Status: Draft

---

# Purpose

This document defines the core business entities of the SHOPLINK Intelligence platform.

Every module in the system must use these entities.

New modules should reuse existing business objects whenever possible.

---

# Core Business Objects

The platform revolves around the following business entities.

These entities represent real-world business concepts rather than technical database tables.

---

# User

Represents an employee using the platform.

Examples:

CEO

Warehouse Employee

Marketing Specialist

Customer Support

Purchasing Manager

Administrator

---

# Role

Defines permissions and visibility.

Examples:

CEO

Warehouse

Marketing

Finance

Support

Administrator

---

# Marketplace

Represents a sales channel.

Examples:

Alza

Mall

Amazon

eMAG

eBay

Official eShop

Future marketplaces

---

# Store

Represents a webshop or business entity.

One company may operate multiple stores.

---

# Product

Represents a sellable product.

Each product contains:

SKU

EAN

Name

Brand

Category

Status

Purchase Price

Selling Price

Current Stock

Lifecycle

AI Score

---

# Product Variant

Optional child of Product.

Example:

Different colour

Different capacity

Different size

---

# Brand

Manufacturer or product brand.

---

# Category

Hierarchical product classification.

---

# Supplier

Company supplying products.

Contains:

Supplier Information

Lead Time

Payment Terms

Performance Metrics

---

# Inventory

Current stock information.

Tracks:

Current Stock

Reserved Stock

Available Stock

Incoming Stock

Safety Stock

---

# Stock Movement

Represents every inventory change.

Examples:

Purchase

Sale

Return

Complaint

Manual Correction

Inventory Count

---

# Purchase

Purchase order from supplier.

---

# Purchase Item

Individual purchased product.

---

# Customer

Represents buyer information where required.

---

# Order

Represents customer order.

Imported from Mobdata.

Contains:

Marketplace

Country

Date

Payment

Shipping

Status

Profit

---

# Order Item

Single product inside an order.

---

# Shipment

Shipping information.

---

# Invoice

Commercial document.

Only stored when required.

Accounting remains external.

---

# Complaint (RMA)

Represents customer complaint.

Contains complete complaint lifecycle.

Statuses:

Received

Inspection

Waiting for Supplier

Resolved

Rejected

Closed

---

# Task

Represents actionable work.

Tasks may be:

Manual

Automatically generated

AI generated

Tasks may originate from:

Inventory

RMA

Sales

Deadlines

System Warnings

---

# Notification

Represents system alerts.

Examples:

Low Stock

High Complaint Rate

Missing Data

Failed Import

Important KPI Change

---

# AI Insight

AI generated recommendation.

Examples:

Increase Price

Reorder Product

Investigate Complaint Rate

Reduce Inventory

Increase Marketing Budget

---

# Import Job

Represents data import execution.

Tracks:

Start Time

Finish Time

Errors

Warnings

Imported Records

Skipped Records

---

# Report

Represents generated business reports.

Reports may be:

Scheduled

Manual

Exported

Shared

---

# Relationships

All future modules must reuse these business entities.

No duplicate business concepts should be introduced.

This document acts as the foundation for database architecture.

---

End of Document