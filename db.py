import mysql.connector
import os

def get_connection(include_db=True):
    config = {
        'host': 'localhost',
        'user': 'root',
        'password': 'tiger'
    }
    if include_db:
        config['database'] = 'traffic_violation_db'
    return mysql.connector.connect(**config)

def check_mysql_status():
    try:
        conn = get_connection(include_db=False)
        conn.close()
        return True, None
    except Exception as e:
        return False, str(e)

def initialize_database(sql_file_path):
    try:
        conn = get_connection(include_db=False)
        cursor = conn.cursor()
        
        if not os.path.exists(sql_file_path):
            return False, f"SQL file not found at: {sql_file_path}"
            
        with open(sql_file_path, 'r', encoding='utf-8') as f:
            sql_script = f.read()
            
        queries = []
        current_query = []
        for line in sql_script.splitlines():
            stripped = line.strip()
            if not stripped or stripped.startswith('--') or stripped.startswith('#'):
                continue
            current_query.append(line)
            if stripped.endswith(';'):
                queries.append('\n'.join(current_query))
                current_query = []
                
        for query in queries:
            if query.strip():
                cursor.execute(query)
                
        conn.commit()
        cursor.close()
        conn.close()
        return True, None
    except Exception as e:
        return False, str(e)
