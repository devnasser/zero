from datetime import datetime
from airflow import DAG
from airflow.providers.docker.operators.docker import DockerOperator

MEMBERS = [
    "bc-4b7c40ee-61da-4e40-a1b5-00130ebeab05",
    "bc-c6ad5e89-6078-478a-bc8f-9a0a9b2eaf07",
    "bc-53e6b4fb-f99d-4529-8009-87cded370c2e",
    "bc-6f51d46b-acaf-4420-bfcc-dde383262ffd",
    "bc-cd787b2b-846c-408f-af2e-8a5e6dcb7034",
    "bc-d53b7194-4c63-47f4-bbec-9c3d1733c0da",
    "bc-66385910-81dd-4a15-a5c2-898ca178f28f",
    "bc-2a36493e-cecb-41f8-9649-fbaa8065651b",
    "bc-36b47a15-eb80-4e9d-b43a-92a03d37dd68",
    "bc-b8a7ba53-02bb-4085-8b00-f48ba8692c72",
    "bc-f2de85d5-4b15-44df-b29e-67063e1ee43e",
    "bc-d9db207a-9c0f-4c8f-9d0e-6765fb09fe17",
    "bc-c6862013-0867-4580-a63e-ec3dcfe80612",
    "bc-769fce79-ac2e-47ac-9b4b-dcd441c51f57",
    "bc-ac204b85-e66f-47bd-b94f-5a72258e758b",
    "bc-757ca27c-5f17-4639-be3a-c40db5ad84cb",
    "bc-29647012-64a3-410a-8551-5829d66c5c2f",
    "bc-379f5c75-324c-4808-8f15-327c28a7bb01",
    "bc-21237909-c807-4d00-8870-ed90dca01651",
    "bc-3705fce2-ce8c-467c-b7e7-05c19f29128e",
    "bc-a271ece5-14e1-40bf-b45a-226ad6a6aa80",
    "bc-84e08487-7dec-4dfb-9eec-3c224e2b8424",
]

with DAG(
    dag_id="team_tasks",
    schedule_interval=None,
    start_date=datetime(2025, 8, 11),
    catchup=False,
    max_active_tasks=4,
    tags=["team"],
) as dag:
    for member in MEMBERS:
        DockerOperator(
            task_id=f"run_{member}",
            image="team-base:1.0",
            command=f"bash -c 'echo Running task for {member}'",
            auto_remove=True,
            cpus=1,
            mem_limit="2g",
            container_name=member,
            tty=True,
        )