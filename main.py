import customtkinter as ctk
from tkinter import ttk, messagebox
import db
from datetime import datetime
import os

ctk.set_appearance_mode("dark")
ctk.set_default_color_theme("blue")

class SmartTrafficApp(ctk.CTk):
    def __init__(self):
        super().__init__()
        
        self.title("Smart Traffic Violation Monitoring System")
        self.geometry("1400x850")
        self.configure(fg_color="#0b0f19")
        
        self.verify_database()
        self.setup_table_style()
        self.build_ui()
        
    def verify_database(self):
        online, error = db.check_mysql_status()
        if not online:
            messagebox.showerror(
                "Database Error",
                f"Unable to connect to local MySQL Server.\n\n"
                f"Error: {error}\n\n"
                f"Ensure MySQL is running and password is set to 'tiger'."
            )
            self.destroy()
            exit()
            
        try:
            conn = db.get_connection(include_db=True)
            conn.close()
        except Exception:
            sql_path = os.path.join(os.getcwd(), 'db', 'database.sql')
            success, init_err = db.initialize_database(sql_path)
            if success:
                messagebox.showinfo(
                    "Database Setup",
                    "Database has been successfully initialized and seeded with sample data."
                )
            else:
                messagebox.showerror(
                    "Database Initialization Failed",
                    f"Unable to initialize database schema: {init_err}"
                )
                self.destroy()
                exit()

    def setup_table_style(self):
        style = ttk.Style()
        style.theme_use("clam")
        
        style.configure(
            "Treeview",
            background="#151c2e",
            foreground="#f8fafc",
            rowheight=35,
            fieldbackground="#151c2e",
            bordercolor="#263352",
            borderwidth=1,
            font=("Arial", 11)
        )
        style.map(
            'Treeview',
            background=[('selected', '#3b82f6')],
            foreground=[('selected', '#ffffff')]
        )
        
        style.configure(
            "Treeview.Heading",
            background="#0b0f19",
            foreground="#94a3b8",
            relief="flat",
            font=("Arial", 11, "bold")
        )
        style.map(
            "Treeview.Heading",
            background=[('active', '#1e293b')],
            foreground=[('active', '#3b82f6')]
        )

    def build_ui(self):
        self.sidebar_frame = ctk.CTkFrame(self, width=260, corner_radius=0, fg_color="#151c2e", border_color="#263352", border_width=1)
        self.sidebar_frame.pack(side="left", fill="y")
        self.sidebar_frame.pack_propagate(False)
        
        self.content_frame = ctk.CTkFrame(self, fg_color="#0b0f19", corner_radius=0, border_width=0)
        self.content_frame.pack(side="right", fill="both", expand=True)
        
        brand_container = ctk.CTkFrame(self.sidebar_frame, fg_color="transparent")
        brand_container.pack(fill="x", pady=25, padx=20)
        
        logo_text = ctk.CTkLabel(brand_container, text="TRAFFIC SYSTEM", font=("Arial", 20, "bold"), text_color="#3b82f6")
        logo_text.pack(side="left", padx=10)
        
        divider = ctk.CTkFrame(self.sidebar_frame, height=1, fg_color="#263352")
        divider.pack(fill="x", padx=15, pady=(0, 20))
        
        self.menu_buttons = {}
        menu_items = [
            "Owners",
            "Vehicles",
            "Violations",
            "Add Entry"
        ]
        
        for name in menu_items:
            btn = ctk.CTkButton(
                self.sidebar_frame,
                text=f"  {name}",
                font=("Arial", 14, "bold"),
                anchor="w",
                height=45,
                corner_radius=8,
                fg_color="transparent",
                text_color="#94a3b8",
                hover_color="#1c273e",
                command=lambda n=name: self.switch_tab(n)
            )
            btn.pack(fill="x", padx=15, pady=5)
            self.menu_buttons[name] = btn
            
        footer_label = ctk.CTkLabel(
            self.sidebar_frame,
            text="Smart Traffic DBMS System",
            font=("Arial", 10),
            text_color="#94a3b8"
        )
        footer_label.pack(side="bottom", pady=20)
        
        self.tabs = {
            "Owners": OwnersTab(self.content_frame, self),
            "Vehicles": VehiclesTab(self.content_frame, self),
            "Violations": ViolationsTab(self.content_frame, self),
            "Add Entry": AddEntryTab(self.content_frame, self)
        }
        
        self.switch_tab("Owners")
        
    def switch_tab(self, tab_name):
        for name, tab in self.tabs.items():
            if name == tab_name:
                tab.pack(fill="both", expand=True)
                tab.on_show()
                self.menu_buttons[name].configure(fg_color="#3b82f6", text_color="#ffffff")
            else:
                tab.pack_forget()
                self.menu_buttons[name].configure(fg_color="transparent", text_color="#94a3b8")

# Owners View Tab
class OwnersTab(ctk.CTkFrame):
    def __init__(self, parent, controller):
        super().__init__(parent, fg_color="transparent")
        self.controller = controller
        
        self.grid_container = ctk.CTkFrame(self, fg_color="#151c2e", border_color="#263352", border_width=1, corner_radius=12)
        self.grid_container.pack(fill="both", expand=True, padx=30, pady=30)
        
        grid_header = ctk.CTkFrame(self.grid_container, fg_color="transparent")
        grid_header.pack(fill="x", padx=25, pady=20)
        
        grid_title = ctk.CTkLabel(grid_header, text="Owners Database", font=("Arial", 18, "bold"), text_color="#f8fafc")
        grid_title.pack(side="left")
        
        self.search_entry = ctk.CTkEntry(grid_header, placeholder_text="Search owners...", width=250, fg_color="#0b0f19", border_color="#263352")
        self.search_entry.pack(side="right")
        self.search_entry.bind("<KeyRelease>", lambda e: self.load_data())
        
        table_frame = ctk.CTkFrame(self.grid_container, fg_color="transparent")
        table_frame.pack(fill="both", expand=True, padx=25, pady=(0, 25))
        
        cols = ("Owner ID", "Name", "License No")
        self.table = ttk.Treeview(table_frame, columns=cols, show="headings")
        for col in cols:
            self.table.heading(col, text=col)
            self.table.column(col, anchor="center")
            
        scrollbar = ttk.Scrollbar(table_frame, orient="vertical", command=self.table.yview)
        self.table.configure(yscrollcommand=scrollbar.set)
        
        self.table.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")
        
    def load_data(self):
        for row in self.table.get_children():
            self.table.delete(row)
        try:
            conn = db.get_connection()
            cursor = conn.cursor()
            search = f"%{self.search_entry.get()}%"
            query = "SELECT Owner_ID, Name, License_No FROM OWNER WHERE Name LIKE %s OR License_No LIKE %s ORDER BY Owner_ID DESC"
            cursor.execute(query, (search, search))
            for item in cursor.fetchall():
                self.table.insert("", "end", values=item)
            cursor.close()
            conn.close()
        except Exception as e:
            messagebox.showerror("Error", str(e))
            
    def on_show(self):
        self.load_data()

# Vehicles View Tab
class VehiclesTab(ctk.CTkFrame):
    def __init__(self, parent, controller):
        super().__init__(parent, fg_color="transparent")
        self.controller = controller
        
        self.grid_container = ctk.CTkFrame(self, fg_color="#151c2e", border_color="#263352", border_width=1, corner_radius=12)
        self.grid_container.pack(fill="both", expand=True, padx=30, pady=30)
        
        grid_header = ctk.CTkFrame(self.grid_container, fg_color="transparent")
        grid_header.pack(fill="x", padx=25, pady=20)
        
        grid_title = ctk.CTkLabel(grid_header, text="Vehicles Database", font=("Arial", 18, "bold"), text_color="#f8fafc")
        grid_title.pack(side="left")
        
        self.search_entry = ctk.CTkEntry(grid_header, placeholder_text="Search vehicles...", width=250, fg_color="#0b0f19", border_color="#263352")
        self.search_entry.pack(side="right")
        self.search_entry.bind("<KeyRelease>", lambda e: self.load_data())
        
        table_frame = ctk.CTkFrame(self.grid_container, fg_color="transparent")
        table_frame.pack(fill="both", expand=True, padx=25, pady=(0, 25))
        
        cols = ("Vehicle No", "Model", "Owner")
        self.table = ttk.Treeview(table_frame, columns=cols, show="headings")
        for col in cols:
            self.table.heading(col, text=col)
            self.table.column(col, anchor="center")
            
        scrollbar = ttk.Scrollbar(table_frame, orient="vertical", command=self.table.yview)
        self.table.configure(yscrollcommand=scrollbar.set)
        
        self.table.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")
        
    def load_data(self):
        for row in self.table.get_children():
            self.table.delete(row)
        try:
            conn = db.get_connection()
            cursor = conn.cursor()
            search = f"%{self.search_entry.get()}%"
            query = """
                SELECT V.Vehicle_No, V.Model, CONCAT(O.Owner_ID, ' - ', O.Name) AS Owner_Info
                FROM VEHICLE V
                JOIN OWNER O ON V.Owner_ID = O.Owner_ID
                WHERE V.Vehicle_No LIKE %s OR V.Model LIKE %s OR O.Name LIKE %s
                ORDER BY V.Vehicle_No ASC
            """
            cursor.execute(query, (search, search, search))
            for item in cursor.fetchall():
                self.table.insert("", "end", values=item)
            cursor.close()
            conn.close()
        except Exception as e:
            messagebox.showerror("Error", str(e))
            
    def on_show(self):
        self.load_data()

# Violations View Tab
class ViolationsTab(ctk.CTkFrame):
    def __init__(self, parent, controller):
        super().__init__(parent, fg_color="transparent")
        self.controller = controller
        
        self.grid_container = ctk.CTkFrame(self, fg_color="#151c2e", border_color="#263352", border_width=1, corner_radius=12)
        self.grid_container.pack(fill="both", expand=True, padx=30, pady=30)
        
        grid_header = ctk.CTkFrame(self.grid_container, fg_color="transparent")
        grid_header.pack(fill="x", padx=25, pady=20)
        
        grid_title = ctk.CTkLabel(grid_header, text="Violations Database", font=("Arial", 18, "bold"), text_color="#f8fafc")
        grid_title.pack(side="left")
        
        self.search_entry = ctk.CTkEntry(grid_header, placeholder_text="Search violations...", width=250, fg_color="#0b0f19", border_color="#263352")
        self.search_entry.pack(side="right")
        self.search_entry.bind("<KeyRelease>", lambda e: self.load_data())
        
        table_frame = ctk.CTkFrame(self.grid_container, fg_color="transparent")
        table_frame.pack(fill="both", expand=True, padx=25, pady=(0, 25))
        
        cols = ("Violation ID", "Vehicle Plate", "Violation Type", "Fine", "Status")
        self.table = ttk.Treeview(table_frame, columns=cols, show="headings")
        for col in cols:
            self.table.heading(col, text=col)
            self.table.column(col, anchor="center")
            
        scrollbar = ttk.Scrollbar(table_frame, orient="vertical", command=self.table.yview)
        self.table.configure(yscrollcommand=scrollbar.set)
        
        self.table.pack(side="left", fill="both", expand=True)
        scrollbar.pack(side="right", fill="y")
        
    def load_data(self):
        for row in self.table.get_children():
            self.table.delete(row)
        try:
            conn = db.get_connection()
            cursor = conn.cursor()
            search = f"%{self.search_entry.get()}%"
            query = """
                SELECT Violation_ID, Vehicle_No, Type, Fine_Amount, Status 
                FROM VIOLATION 
                WHERE Vehicle_No LIKE %s OR Type LIKE %s OR Status LIKE %s 
                ORDER BY Violation_ID DESC
            """
            cursor.execute(query, (search, search, search))
            for item in cursor.fetchall():
                fmt_fine = f"₹{float(item[3]):,.2f}"
                self.table.insert("", "end", values=(item[0], item[1], item[2], fmt_fine, item[4]))
            cursor.close()
            conn.close()
        except Exception as e:
            messagebox.showerror("Error", str(e))
            
    def on_show(self):
        self.load_data()


# Consolidated Add Entry Tab (Single Unified Form)
class AddEntryTab(ctk.CTkFrame):
    def __init__(self, parent, controller):
        super().__init__(parent, fg_color="transparent")
        self.controller = controller
        
        container = ctk.CTkFrame(self, fg_color="#151c2e", border_color="#263352", border_width=1, corner_radius=12)
        container.pack(fill="both", expand=True, padx=30, pady=30)
        
        scroll_frame = ctk.CTkScrollableFrame(container, fg_color="transparent")
        scroll_frame.pack(fill="both", expand=True, padx=20, pady=20)
        
        title = ctk.CTkLabel(scroll_frame, text="Log Violation", font=("Arial", 18, "bold"), text_color="#f8fafc")
        title.pack(anchor="w", pady=(0, 15))
        
        grid_frame = ctk.CTkFrame(scroll_frame, fg_color="transparent")
        grid_frame.pack(fill="both", expand=True)
        grid_frame.grid_columnconfigure((0, 1), weight=1, uniform="equal")
        
        # Column 0: Owner & Vehicle Details
        col0 = ctk.CTkFrame(grid_frame, fg_color="transparent")
        col0.grid(row=0, column=0, padx=(0, 20), sticky="nsew")
        
        owner_sub = ctk.CTkLabel(col0, text="Owner Details", font=("Arial", 14, "bold"), text_color="#3b82f6")
        owner_sub.pack(anchor="w", pady=(0, 10))
        
        lbl_oname = ctk.CTkLabel(col0, text="Owner Full Name:", font=("Arial", 11, "bold"), text_color="#94a3b8")
        lbl_oname.pack(anchor="w", pady=(5, 2))
        self.owner_name = ctk.CTkEntry(col0, placeholder_text="Enter Owner Name", height=36, fg_color="#0b0f19", border_color="#263352")
        self.owner_name.pack(fill="x", pady=(0, 10))
        
        lbl_olic = ctk.CTkLabel(col0, text="License Number:", font=("Arial", 11, "bold"), text_color="#94a3b8")
        lbl_olic.pack(anchor="w", pady=(5, 2))
        self.owner_lic = ctk.CTkEntry(col0, placeholder_text="Enter License Number", height=36, fg_color="#0b0f19", border_color="#263352")
        self.owner_lic.pack(fill="x", pady=(0, 20))
        
        veh_sub = ctk.CTkLabel(col0, text="Vehicle Details", font=("Arial", 14, "bold"), text_color="#3b82f6")
        veh_sub.pack(anchor="w", pady=(10, 10))
        
        lbl_vno = ctk.CTkLabel(col0, text="Vehicle Number:", font=("Arial", 11, "bold"), text_color="#94a3b8")
        lbl_vno.pack(anchor="w", pady=(5, 2))
        self.vehicle_no = ctk.CTkEntry(col0, placeholder_text="Enter Vehicle Number (e.g. DL-3C-AB-1234)", height=36, fg_color="#0b0f19", border_color="#263352")
        self.vehicle_no.pack(fill="x", pady=(0, 10))
        
        lbl_vmod = ctk.CTkLabel(col0, text="Model Detail:", font=("Arial", 11, "bold"), text_color="#94a3b8")
        lbl_vmod.pack(anchor="w", pady=(5, 2))
        self.vehicle_model = ctk.CTkEntry(col0, placeholder_text="Enter Model (e.g. Honda City)", height=36, fg_color="#0b0f19", border_color="#263352")
        self.vehicle_model.pack(fill="x", pady=(0, 10))
        
        # Column 1: Violation Details
        col1 = ctk.CTkFrame(grid_frame, fg_color="transparent")
        col1.grid(row=0, column=1, padx=(20, 0), sticky="nsew")
        
        viol_sub = ctk.CTkLabel(col1, text="Violation Details", font=("Arial", 14, "bold"), text_color="#3b82f6")
        viol_sub.pack(anchor="w", pady=(0, 10))
        
        lbl_type = ctk.CTkLabel(col1, text="Violation Type:", font=("Arial", 11, "bold"), text_color="#94a3b8")
        lbl_type.pack(anchor="w", pady=(5, 2))
        self.viol_type_drop = ctk.CTkComboBox(col1, values=["Speed Limit Violation", "Red Light Jumping", "Drunken Driving", "Wrong-side Driving", "No Helmet (Rider)", "Triple Riding"], height=36, fg_color="#0b0f19", border_color="#263352", button_color="#263352")
        self.viol_type_drop.pack(fill="x", pady=(0, 10))
        self.viol_type_drop.set("Speed Limit Violation")
        
        lbl_fine = ctk.CTkLabel(col1, text="Fine Amount (INR):", font=("Arial", 11, "bold"), text_color="#94a3b8")
        lbl_fine.pack(anchor="w", pady=(5, 2))
        self.viol_fine = ctk.CTkEntry(col1, placeholder_text="Enter Fine Amount", height=36, fg_color="#0b0f19", border_color="#263352")
        self.viol_fine.pack(fill="x", pady=(0, 10))
        
        lbl_status = ctk.CTkLabel(col1, text="Payment Status:", font=("Arial", 11, "bold"), text_color="#94a3b8")
        lbl_status.pack(anchor="w", pady=(5, 2))
        self.viol_status_drop = ctk.CTkComboBox(col1, values=["Pending", "Paid"], height=36, fg_color="#0b0f19", border_color="#263352", button_color="#263352")
        self.viol_status_drop.pack(fill="x", pady=(0, 20))
        self.viol_status_drop.set("Pending")
        
        # Submit Button
        btn = ctk.CTkButton(scroll_frame, text="Log Violation Record", fg_color="#3b82f6", hover_color="#60a5fa", font=("Arial", 12, "bold"), width=300, height=44, command=self.submit_citation)
        btn.pack(anchor="w", pady=(25, 0))
        
    def submit_citation(self):
        owner_name = self.owner_name.get().strip()
        owner_lic = self.owner_lic.get().strip().upper()
        vehicle_no = self.vehicle_no.get().strip().upper()
        vehicle_model = self.vehicle_model.get().strip()
        v_type = self.viol_type_drop.get()
        fine_val = self.viol_fine.get().strip()
        status = self.viol_status_drop.get()
        
        if not owner_name or not owner_lic or not vehicle_no or not vehicle_model or not fine_val:
            messagebox.showwarning("Incomplete Fields", "Please complete all Owner, Vehicle, and Violation fields.")
            return
            
        try:
            fine_amount = float(fine_val)
        except ValueError:
            messagebox.showerror("Validation Error", "Fine Amount must be a valid number.")
            return
            
        try:
            conn = db.get_connection()
            cursor = conn.cursor()
            
            # 1. Resolve Owner
            cursor.execute("SELECT Owner_ID FROM OWNER WHERE License_No = %s", (owner_lic,))
            owner_data = cursor.fetchone()
            if owner_data:
                owner_id = owner_data[0]
                cursor.execute("UPDATE OWNER SET Name = %s WHERE Owner_ID = %s", (owner_name, owner_id))
            else:
                cursor.execute("INSERT INTO OWNER (Name, License_No) VALUES (%s, %s)", (owner_name, owner_lic))
                owner_id = cursor.lastrowid
                
            # 2. Resolve Vehicle
            cursor.execute("SELECT Vehicle_No FROM VEHICLE WHERE Vehicle_No = %s", (vehicle_no,))
            vehicle_data = cursor.fetchone()
            if vehicle_data:
                cursor.execute("UPDATE VEHICLE SET Owner_ID = %s, Model = %s WHERE Vehicle_No = %s", (owner_id, vehicle_model, vehicle_no))
            else:
                cursor.execute("INSERT INTO VEHICLE (Vehicle_No, Owner_ID, Model) VALUES (%s, %s, %s)", (vehicle_no, owner_id, vehicle_model))
                
            # 3. Log Violation
            cursor.execute("INSERT INTO VIOLATION (Vehicle_No, Type, Fine_Amount, Status) VALUES (%s, %s, %s, %s)", (vehicle_no, v_type, fine_amount, status))
            
            conn.commit()
            cursor.close()
            conn.close()
            
            messagebox.showinfo("Success", "Incident logged successfully!\nOwner, Vehicle, and Violation details have been updated.")
            
            # Clear fields
            self.owner_name.delete(0, "end")
            self.owner_lic.delete(0, "end")
            self.vehicle_no.delete(0, "end")
            self.vehicle_model.delete(0, "end")
            self.viol_fine.delete(0, "end")
            
        except Exception as e:
            messagebox.showerror("Database Transaction Error", str(e))
            
    def on_show(self):
        pass


if __name__ == "__main__":
    app = SmartTrafficApp()
    app.mainloop()