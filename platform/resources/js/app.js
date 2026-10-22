import "../../vendor/tallstackui/tallstackui/dist/tallstackui-NrxCew1f.js";
import "../../vendor/tallstackui/tallstackui/dist/tallstackui-date-CbEf0FZx.js";
import Alpine from "alpinejs";
import anchor from "@alpinejs/anchor";
import intersect from "@alpinejs/intersect";

window.Alpine = Alpine;

Alpine.plugin(anchor);
Alpine.plugin(intersect);

Alpine.magic("wire", () => ({
    $errors: {
        has: () => false,
        first: () => "",
    },
}));

Alpine.store("taskCenter", {
    openCreate: false,
    openCreatePanel() {
        this.openCreate = true;
    },
    closeCreatePanel() {
        this.openCreate = false;
    },
});

Alpine.start();

document.addEventListener("DOMContentLoaded", () => {
    const urlBase64ToUint8Array = (base64String) => {
        const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
        const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");
        const rawData = window.atob(base64);
        const outputArray = new Uint8Array(rawData.length);

        for (let index = 0; index < rawData.length; index += 1) {
            outputArray[index] = rawData.charCodeAt(index);
        }

        return outputArray;
    };

    const initWebPushPanel = async () => {
        const panel = document.querySelector("[data-web-push-panel]");
        const toggle = panel?.querySelector("[data-web-push-toggle]");
        const status = panel?.querySelector("[data-web-push-status]");

        if (!panel || !toggle || !status) {
            return;
        }

        if (!("serviceWorker" in navigator) || !("PushManager" in window)) {
            status.textContent = "Toto zariadenie nepodporuje Web Push.";
            toggle.disabled = true;
            return;
        }

        const publicKey = panel.dataset.webPushPublicKey;
        const subscribeUrl = panel.dataset.webPushSubscribeUrl;
        const unsubscribeUrl = panel.dataset.webPushUnsubscribeUrl;
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content") || "";

        if (!publicKey) {
            status.textContent = "Web Push ešte nie je nastavený na serveri.";
            toggle.disabled = true;
            return;
        }

        const registration = await navigator.serviceWorker.register("/sw.js");
        let subscription = await registration.pushManager.getSubscription();

        const syncState = () => {
            if (subscription) {
                toggle.textContent = "Vypnúť push";
                status.textContent = "Push notifikácie sú na tomto zariadení zapnuté.";
            } else {
                toggle.textContent = "Zapnúť push";
                status.textContent = "Push notifikácie sú na tomto zariadení vypnuté.";
            }
        };

        syncState();

        toggle.addEventListener("click", async () => {
            toggle.disabled = true;

            try {
                if (!subscription) {
                    const permission = await Notification.requestPermission();

                    if (permission !== "granted") {
                        status.textContent = "Povolenie pre notifikácie nebolo udelené.";
                        return;
                    }

                    subscription = await registration.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: urlBase64ToUint8Array(publicKey),
                    });

                    const subscribeResponse = await fetch(subscribeUrl, {
                        method: "POST",
                        headers: {
                            Accept: "application/json",
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        body: JSON.stringify({
                            endpoint: subscription.endpoint,
                            keys: subscription.toJSON().keys,
                            contentEncoding: subscription.options?.applicationServerKey ? "aes128gcm" : "aes128gcm",
                        }),
                    });

                    if (!subscribeResponse.ok) {
                        throw new Error("Subscribe failed.");
                    }
                } else {
                    const unsubscribeResponse = await fetch(unsubscribeUrl, {
                        method: "DELETE",
                        headers: {
                            Accept: "application/json",
                            "Content-Type": "application/json",
                            "X-CSRF-TOKEN": csrfToken,
                            "X-Requested-With": "XMLHttpRequest",
                        },
                        body: JSON.stringify({
                            endpoint: subscription.endpoint,
                        }),
                    });

                    if (!unsubscribeResponse.ok) {
                        throw new Error("Unsubscribe failed.");
                    }

                    await subscription.unsubscribe();
                    subscription = null;
                }

                syncState();
            } catch (error) {
                status.textContent = "Push sa nepodarilo nastaviť na tomto zariadení.";
            } finally {
                toggle.disabled = false;
            }
        });
    };

    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.register("/sw.js").catch(() => {});
    }

    initWebPushPanel().catch(() => {});

    const createPanel = document.querySelector("[data-task-create-panel]");
    const createOpenButtons = Array.from(document.querySelectorAll("[data-task-create-open]"));
    const createCloseButtons = Array.from(document.querySelectorAll("[data-task-create-close]"));

    createOpenButtons.forEach((button) => {
        button.addEventListener("click", () => {
            window.Alpine?.store("taskCenter")?.openCreatePanel();
        });
    });

    createCloseButtons.forEach((button) => {
        button.addEventListener("click", () => {
            window.Alpine?.store("taskCenter")?.closeCreatePanel();
        });
    });

    document.addEventListener("keydown", (event) => {
        if (event.key === "Escape" && createPanel?.style.display !== "none") {
            window.Alpine?.store("taskCenter")?.closeCreatePanel();
        }
    });

    const editOpenElements = Array.from(document.querySelectorAll("[data-task-edit-open]"));
    const editCloseButtons = Array.from(document.querySelectorAll("[data-task-edit-close]"));

    const hideEditPanel = (panel) => {
        const sheet = panel?.querySelector("[data-task-edit-sheet]");
        const overlay = panel?.querySelector("[data-task-edit-overlay]");

        if (sheet) {
            sheet.style.transform = "translateX(100%)";
        }

        window.setTimeout(() => {
            if (sheet) {
                sheet.style.display = "none";
            }

            if (overlay) {
                overlay.style.display = "none";
            }

            if (panel) {
                panel.style.display = "none";
            }

            document.body.classList.remove("overflow-y-hidden");
        }, 200);
    };

    const showEditPanel = (taskId) => {
        const panel = document.querySelector(`[data-task-edit-panel="${taskId}"]`);
        const sheet = panel?.querySelector("[data-task-edit-sheet]");
        const overlay = panel?.querySelector("[data-task-edit-overlay]");

        if (!panel || !sheet) {
            return;
        }

        document.querySelectorAll("[data-task-edit-panel]").forEach((otherPanel) => {
            if (otherPanel !== panel) {
                otherPanel.style.display = "none";
            }
        });

        panel.style.display = "block";

        if (overlay) {
            overlay.style.display = "block";
        }

        sheet.style.display = "block";
        sheet.style.transform = "translateX(100%)";
        sheet.style.transition = "transform 220ms ease-out";
        requestAnimationFrame(() => {
            sheet.style.transform = "translateX(0)";
        });

        document.body.classList.add("overflow-y-hidden");
    };

    editOpenElements.forEach((element) => {
        element.addEventListener("click", (event) => {
            if (event.target.closest("[data-task-row-ignore]")) {
                return;
            }

            event.preventDefault();
            event.stopPropagation();
            showEditPanel(element.dataset.taskEditOpen);
        });
    });

    editCloseButtons.forEach((button) => {
        button.addEventListener("click", () => {
            hideEditPanel(button.closest("[data-task-edit-panel]"));
        });
    });

    document.addEventListener("keydown", (event) => {
        if (event.key !== "Escape") {
            return;
        }

        const openEditPanel = Array.from(document.querySelectorAll("[data-task-edit-panel]"))
            .find((panel) => panel.style.display !== "none");

        if (openEditPanel) {
            hideEditPanel(openEditPanel);
        }
    });

    const statusMenus = Array.from(document.querySelectorAll(".sl-status-menu"));

    if (!statusMenus.length) {
        return;
    }

    document.addEventListener("click", (event) => {
        statusMenus.forEach((menu) => {
            if (menu.open && !menu.contains(event.target)) {
                menu.open = false;
            }
        });
    });

    statusMenus.forEach((menu) => {
        menu.addEventListener("toggle", () => {
            if (!menu.open) {
                return;
            }

            statusMenus.forEach((otherMenu) => {
                if (otherMenu !== menu) {
                    otherMenu.open = false;
                }
            });
        });
    });

    const toastStack = document.querySelector("[data-toast-stack]");

    const showToast = (message, type = "success") => {
        if (!toastStack) {
            return;
        }

        const toast = document.createElement("div");
        toast.className = `sl-toast sl-toast-${type}`;
        toast.textContent = message;
        toastStack.appendChild(toast);

        window.setTimeout(() => {
            toast.style.opacity = "0";
            toast.style.transform = "translateY(-4px)";
        }, 2400);

        window.setTimeout(() => {
            toast.remove();
        }, 2800);
    };

    const statusClassNames = ["new", "in_progress", "blocked", "done"]
        .map((status) => `sl-task-status-${status}`);

    const swapCommentsHtml = (taskId, html) => {
        const list = document.querySelector(`[data-task-comments-list="${taskId}"]`);

        if (list) {
            list.outerHTML = html;
        }
    };

    const renderDueHtml = (task) => {
        if (!task.due_at_label) {
            return `
                <span class="sl-date-icon" aria-label="Bez termínu">
                    <svg viewBox="0 0 20 20" class="h-4 w-4" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M6 2.75a.75.75 0 0 1 .75.75V4h6.5v-.5a.75.75 0 0 1 1.5 0V4H16a2 2 0 0 1 2 2v8.25a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h1.25v-.5A.75.75 0 0 1 6 2.75ZM3.5 8v6.25a.5.5 0 0 0 .5.5h12a.5.5 0 0 0 .5-.5V8h-13Z" clip-rule="evenodd" />
                    </svg>
                </span>
            `;
        }

        const mobileClass = task.due_state === "overdue"
            ? "sl-due-chip sl-due-chip-overdue"
            : (task.due_state === "today" ? "sl-due-chip sl-due-chip-today" : "bg-slate-700/72 text-slate-200");

        const desktopClass = task.due_state === "overdue"
            ? "sl-dashboard-task-date sl-dashboard-task-date-overdue"
            : (task.due_state === "today" ? "sl-dashboard-task-date sl-dashboard-task-date-today" : "sl-dashboard-task-date text-slate-300");

        return {
            mobile: `<span class="rounded-md px-2 py-1 font-medium ${mobileClass}">${task.due_at_label}</span>`,
            desktop: `<span class="${desktopClass}" style="margin-left:4px;">${task.due_at_label}</span>`,
        };
    };

    const syncTaskSnapshot = (task) => {
        document.querySelectorAll(`[data-task-title="${task.id}"]`).forEach((element) => {
            element.textContent = task.title;
            element.setAttribute("title", task.title);
        });

        document.querySelectorAll(`[data-task-panel-title="${task.id}"]`).forEach((element) => {
            element.textContent = task.title;
        });

        document.querySelectorAll(`[data-order-complete-toggle][data-task-id="${task.id}"]`).forEach((element) => {
            const completed = task.status === "done";
            element.setAttribute("aria-checked", completed ? "true" : "false");
            element.setAttribute("aria-label", completed ? "Označiť úlohu ako novú" : "Označiť úlohu ako vybavenú");
            const label = element.querySelector("[data-order-complete-label]");
            if (label) {
                label.textContent = completed ? "Vybavená" : "Nová";
            }
        });

        document.querySelectorAll(`[data-task-edit-panel="${task.id}"] input[name="status"]`).forEach((element) => {
            element.value = task.status;
        });

        const dueHtml = renderDueHtml(task);

        document.querySelectorAll(`[data-task-mobile-due="${task.id}"]`).forEach((element) => {
            element.innerHTML = typeof dueHtml === "string" ? dueHtml : dueHtml.mobile;
        });

        document.querySelectorAll(`[data-task-desktop-due="${task.id}"]`).forEach((element) => {
            element.innerHTML = typeof dueHtml === "string" ? dueHtml : dueHtml.desktop;
        });

        swapCommentsHtml(task.id, task.comments_html);
    };

    document.addEventListener("click", async (event) => {
        const toggle = event.target.closest("[data-order-complete-toggle]");

        if (!toggle) {
            return;
        }

        event.preventDefault();
        event.stopPropagation();

        const taskId = toggle.dataset.taskId;
        const completed = toggle.getAttribute("aria-checked") === "true";
        const formData = new FormData();
        formData.set("_token", document.querySelector('meta[name="csrf-token"]')?.content || "");
        formData.set("_method", "PATCH");
        formData.set("status", completed ? "new" : "done");
        toggle.setAttribute("disabled", "disabled");

        try {
            const response = await fetch(toggle.dataset.taskStatusAction, {
                method: "POST",
                body: formData,
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error("Order status update failed.");
            }

            const payload = await response.json();
            syncTaskSnapshot(payload.task);

            const taskCenter = document.querySelector("[data-task-show-done]");
            const shouldHide = payload.task.status !== "done" && taskCenter?.dataset.taskStatusFilter === "done";

            if (shouldHide) {
                window.setTimeout(() => {
                    const row = document.querySelector(`[data-task-row-id="${taskId}"]`);
                    const section = row?.closest("[data-task-category-section]");
                    row?.remove();

                    const remainingRows = section?.querySelectorAll("[data-task-row-id]").length || 0;
                    if (remainingRows === 0) {
                        section?.remove();
                    } else {
                        const count = section?.querySelector("[data-task-category-count]");
                        if (count) {
                            count.textContent = `(${remainingRows})`;
                        }
                    }
                }, 180);
            }

            showToast(payload.message || "Stav objednávky bol uložený.");
        } catch (error) {
            showToast("Stav objednávky sa nepodarilo uložiť.", "error");
        } finally {
            toggle.removeAttribute("disabled");
        }
    });

    document.addEventListener("click", (event) => {
        const toggle = event.target.closest("[data-comment-edit-toggle]");
        const cancel = event.target.closest("[data-comment-edit-cancel]");

        if (toggle) {
            const commentCard = toggle.closest("[data-task-comment]");
            const editForm = commentCard?.querySelector("[data-task-comment-edit-form]");
            if (editForm) {
                editForm.classList.remove("hidden");
                toggle.classList.add("hidden");
            }
        }

        if (cancel) {
            const editForm = cancel.closest("[data-task-comment-edit-form]");
            const commentCard = cancel.closest("[data-task-comment]");
            const toggleButton = commentCard?.querySelector("[data-comment-edit-toggle]");
            if (editForm) {
                editForm.classList.add("hidden");
            }
            if (toggleButton) {
                toggleButton.classList.remove("hidden");
            }
        }
    });

    document.querySelectorAll("[data-task-comment-form]").forEach((form) => {
        form.addEventListener("submit", async (event) => {
            event.preventDefault();

            const taskId = form.dataset.taskId;
            const submitButton = form.querySelector('button[type="submit"]');
            const textarea = form.querySelector('textarea[name="body"]');
            const formData = new FormData(form);

            submitButton?.setAttribute("disabled", "disabled");

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                if (!response.ok) {
                    throw new Error("Comment create failed.");
                }

                const payload = await response.json();
                swapCommentsHtml(taskId, payload.comments_html);
                form.reset();
                if (textarea) {
                    textarea.value = "";
                }
                showToast(payload.message || "Komentár bol pridaný.");
            } catch (error) {
                showToast("Komentár sa nepodarilo uložiť.", "error");
            } finally {
                submitButton?.removeAttribute("disabled");
            }
        });
    });

    document.addEventListener("submit", async (event) => {
        const form = event.target.closest("[data-task-comment-edit-form]");

        if (!form) {
            return;
        }

        event.preventDefault();

        const taskPanel = form.closest("[data-task-edit-panel]");
        const taskId = taskPanel?.dataset.taskEditPanel;
        const submitButton = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

        submitButton?.setAttribute("disabled", "disabled");

        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error("Comment update failed.");
            }

            const payload = await response.json();
            swapCommentsHtml(taskId, payload.comments_html);
            showToast(payload.message || "Komentár bol upravený.");
        } catch (error) {
            showToast("Komentár sa nepodarilo upraviť.", "error");
        } finally {
            submitButton?.removeAttribute("disabled");
        }
    });

    document.addEventListener("submit", async (event) => {
        const form = event.target.closest("[data-task-comment-delete-form]");

        if (!form) {
            return;
        }

        event.preventDefault();

        if (!window.confirm("Naozaj chceš zmazať tento komentár?")) {
            return;
        }

        const taskPanel = form.closest("[data-task-edit-panel]");
        const taskId = taskPanel?.dataset.taskEditPanel;
        const submitButton = form.querySelector('button[type="submit"]');
        const formData = new FormData(form);

        submitButton?.setAttribute("disabled", "disabled");

        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    Accept: "application/json",
                    "X-Requested-With": "XMLHttpRequest",
                },
            });

            if (!response.ok) {
                throw new Error("Comment delete failed.");
            }

            const payload = await response.json();
            swapCommentsHtml(taskId, payload.comments_html);
            showToast(payload.message || "Komentár bol zmazaný.");
        } catch (error) {
            showToast("Komentár sa nepodarilo zmazať.", "error");
        } finally {
            submitButton?.removeAttribute("disabled");
        }
    });

    document.querySelectorAll("[data-task-status-form]").forEach((form) => {
        form.addEventListener("submit", async (event) => {
            event.preventDefault();

            const details = form.closest("[data-task-status-menu]");
            const summary = details?.querySelector("[data-task-status-summary]");
            const label = details?.querySelector("[data-task-status-label]");
            const button = form.querySelector("[data-task-status-option]");
            const formData = new FormData(form);

            button?.setAttribute("disabled", "disabled");

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                if (!response.ok) {
                    throw new Error("Status update failed.");
                }

                const payload = await response.json();
                const task = payload.task;

                if (task) {
                    syncTaskSnapshot(task);
                } else {
                    const status = payload.task?.status;
                    const statusLabel = payload.task?.status_label;

                    if (summary && status) {
                        summary.classList.remove(...statusClassNames);
                        summary.classList.add(`sl-task-status-${status}`);
                    }

                    if (label && statusLabel) {
                        label.textContent = statusLabel;
                    }
                }

                details.open = false;
                showToast(payload.message || "Stav úlohy bol uložený.");
            } catch (error) {
                showToast("Stav sa nepodarilo uložiť.", "error");
            } finally {
                button?.removeAttribute("disabled");
            }
        });
    });

    const startTaskSnapshotPolling = () => {
        const syncUrl = document.querySelector('meta[name="csrf-token"]')
            ? "/tasks/snapshot"
            : null;

        if (!syncUrl) {
            return;
        }

        let polling = false;

        const poll = async () => {
            if (polling || document.hidden) {
                return;
            }

            const ids = Array.from(document.querySelectorAll("[data-task-row-id]"))
                .map((element) => Number.parseInt(element.dataset.taskRowId || "", 10))
                .filter((value) => Number.isInteger(value));

            if (!ids.length) {
                return;
            }

            polling = true;

            try {
                const query = new URLSearchParams();
                ids.forEach((id) => query.append("ids[]", String(id)));

                const response = await fetch(`${syncUrl}?${query.toString()}`, {
                    headers: {
                        Accept: "application/json",
                        "X-Requested-With": "XMLHttpRequest",
                    },
                });

                if (!response.ok) {
                    throw new Error("Snapshot sync failed.");
                }

                const payload = await response.json();
                (payload.tasks || []).forEach(syncTaskSnapshot);
            } catch (error) {
                // silent background sync
            } finally {
                polling = false;
            }
        };

        window.setInterval(poll, 15000);
        document.addEventListener("visibilitychange", () => {
            if (!document.hidden) {
                poll();
            }
        });
    };

    startTaskSnapshotPolling();
});
